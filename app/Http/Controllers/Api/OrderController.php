<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Token;
use App\Models\User;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'status' => 'required|in:all,pending,confirmed,processing,delivered,declined',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user_type = User::where('id',$request->user_id)->select('type')->first()->type;

        $orders = Order::where($user_type.'_id',$request->user_id)->where(function($q) use($request)
        {
            if($request->status != 'all') $q->where('status',$request->status);
        })->latest()->select('id','status','code','seller_id','buyer_id','items_count','created_at as timestamp')->paginate(20);

        $merchant_type_id = $user_type == 'seller' ? 'buyer_id' : 'seller_id';
        foreach($orders as $order)
        {
            $order['merchant'] = User::where('id',$order->$merchant_type_id)->select('id','first_name','last_name','image')->first();
            unset($order->buyer_id,$order->seller_id);
        }

        return r_json($orders);
    }


    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'order_id' => 'required','exists:orders,id,deleted,0'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $order = Order::where('id',$request->order_id)->select('id','status','code','seller_id','buyer_id','address_id as address','items','items_count','items_fee','tax_fee','total_fee','created_at as timestamp','image')->first();
        if (! ($order->seller_id != $request->user_id || $order->buyer_id != $request->user_id)) return r_json(['msg' => word('user_not_allowed')],401);

        $user_type = User::where('id',$request->user_id)->select('type')->first()->type;
        $merchant_type_id = $user_type == 'seller' ? 'buyer_id' : 'seller_id';

        $order['address'] = $order->address ? Address::where('id',$order->address)->select('text')->withoutGlobalScopes()->first()->text : '';
        $order['merchant'] = User::where('id',$order->$merchant_type_id)->select('id','first_name','last_name','image','bank_info')->withoutGlobalScopes()->first();
        $order['status_lang'] = word($order->status);

        $items = new Collection();

        foreach($order->items as $item)
        {
            $product = Product::where('id',$item->product_id)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->first();
            if($product->type == 'variable')
            {
                $variation = ProductVariation::where('product_id',$item->product_id)->where('id',$item->product_variation_id)->select('options','price','sale_price')->first();

                $arr = [];
                $str_arr = [];

                foreach($variation->options as $option_id) $arr[] = VariationOption::where('id',$option_id)->select(lang().'_name as name')->first()->name;
                $arr['options_str'] = implode(' - ',$str_arr);
                $arr['price'] = $item->price;
                $arr['sale_price'] = $item->price;
                $arr['count'] = $item->count;
            }
            else
            {
                $arr['options_str'] = '';
                $arr['price'] = $item->price;
                $arr['sale_price'] = $item->price;
                $arr['count'] = $item->count;
            }

            $items->push($product);
        }

        $order['items'] = $items;
        unset($order->type,$order->buyer_id,$order->seller_id);

        return r_json($order,200,'object');
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'order_id' => 'required','exists:orders,id,deleted,0,buyer_id,'.$request->user_id,
                'image' => 'required|image'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/orders/'.$info['month']),$info['image']);

        $order = Order::where('id',$request->order_id)->select('id','code','seller_id','image')->first();
            $order->image = $info['name'];
        $order->save();

        $ar_text = 'تم تقديم صورة التحويل البنكي ل طلبك #'.$order->code;
        $en_text = 'An image for a bank transfer has been submitted to you order #'.$order->code;

        $notification = Notification::create
        (
            [
                'type' => 'order',
                'action_id' => $order->id,
                'user_id' => $order->seller_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text,
            ]
        );

        $token = Token::where('user_id',$order->seller_id)->orderBy('updated_at', 'desc')->pluck('token');

        $data['body'] = $notification->user->lang == 'ar' ? $ar_text : $en_text;
        $data['click_id'] = (integer)$order->id;
        $data['click_action'] = 'order';

        Notification::send($token,$data);

        return r_json([],204);
    }


    public function change_status(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'order_id' => 'required|exists:orders,id,deleted,0,seller_id,'.$request->user_id,
                'status' => 'required|in:confirmed,processing,delivered,declined'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }


        $order = Order::where('id',$request->order_id)->select('id','status','code','buyer_id')->first();
            $order->status = $request->status;
        $order->save();

        $ar_text = 'تم تغيير حالة طلبك #'.$order->code;
        $en_text = 'Your Order #'.$order->code.' status has been updated';

        $notification = Notification::create
        (
            [
                'type' => 'order',
                'action_id' => $order->id,
                'user_id' => $order->buyer_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text,
            ]
        );

        $token = Token::where('user_id',$order->buyer_id)->orderBy('updated_at', 'desc')->pluck('token');

        $data['body'] = $notification->user->lang == 'ar' ? $ar_text : $en_text;
        $data['click_id'] = (integer)$order->id;
        $data['click_action'] = 'order';

        Notification::send($token,$data);

        return r_json([],204);
    }
}
