<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Token;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $orders = Order::where('seller_id',user()->id)->orWhere('buyer_id',user()->id)->where(function($q) use($inputs)
        {
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->paginate();

        $orders_count = Order::where('seller_id',user()->id)->orWhere('buyer_id',user()->id)->count();

        return view('web.orders.index',get_defined_vars());
    }


    public function show($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
        [
            'id' => ['required','exists:orders,id,deleted,0',function ($attribute,$value,$fail) use($id)
            {
                $order = Order::where('id',$id)->select('seller_id','buyer_id')->first();
                if (!(user()->id == $order->seller_id || user()->id == $order->buyer_id)) $fail('not authorized user');
            }]
        ]);

        $order = Order::find($id);

        $items = new Collection();

        foreach($order->items as $item)
        {
            $product = Product::where('id',$item->product_id)->select('id','type',lang().'_name as name','thumb_image as image')->first();
            if($product->type == 'variable')
            {
                $variation = ProductVariation::where('product_id',$item->product_id)->where('id',$item->product_variation_id)->select('options')->first();

                $arr = [];
                $str_arr = [];

                foreach($variation->options as $option_id) $arr[] = VariationOption::where('id',$option_id)->select(lang().'_name as name')->first()->name;
                $arr['options_str'] = implode(' - ',$str_arr);
                $arr['price'] = $item->price;
                $arr['count'] = $item->count;
            }
            else
            {
                $arr['options_str'] = '';
                $arr['price'] = $item->price;
                $arr['count'] = $item->count;
            }

            $product['price'] = $arr;

            $items->push($product);
        }

        $order['items'] = $items;
        $order['address'] = Address::withoutGlobalScopes()->find($order->address_id);

        if($order->status == 'pending') $next = 'confirmed';
        elseif($order->status == 'confirmed') $next = 'processing';
        elseif($order->status == 'processing') $next = 'delivered';

        return view('web.orders.show',get_defined_vars());
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:orders,id,deleted,0,seller_id,'.user()->id,
                'image' => 'required|image'
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/orders/'.$info['month']),$info['image']);

        $order = Order::where('id',$request->id)->select('id','code','seller_id','image')->first();
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

        return back()->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:orders,id,deleted,0,seller_id,'.user()->id,
                'status' => 'sometimes|in:next,declined'
            ]
        );

        $order = Order::where('id',$request->id)->select('id','status','code','buyer_id')->first();
            if($request->status == 'declined') $order->status = 'declined';
            else
            {
                if($order->status == 'pending') $next = 'confirmed';
                elseif($order->status == 'confirmed') $next = 'processing';
                elseif($order->status == 'processing') $next = 'delivered';
                $order->status = $next;
            }
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

        return back()->with('success','updated');
    }
}
