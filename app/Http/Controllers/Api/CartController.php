<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,type,buyer,jwt,'.jwt(),
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $carts = Cart::where('user_id',$request->user_id)->select('id','product_id','product_variation_id','count','price')->paginate(21);
        foreach($carts as $cart)
        {
            $product = Product::where('id',$cart->product_id)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->first();

            if($product->type == 'variable')
            {
                $product['price_meta'] = ProductVariation::where('id',$cart->product_variation_id)->select('price','sale_price','sale','sale_till','count')->first();
            }

            $cart['product_variation_id'] = (integer)$cart->product_variation_id;
            $cart['product'] = $product;
        }



        $totals['cart'] = round(Cart::where('user_id',$request->user_id)->sum('price'),2);
        $totals['tax'] = round(User::getTaxPercentage($request->user_id,$totals['cart']),2);
        $totals['total'] = $totals['cart'] + $totals['tax'];

        $addresses = Address::where('user_id',$request->user_id)->select('id','text')->get();

        return r_json(['carts' =>$carts,'totals' => $totals,'addresses' => $addresses],200,'array');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'product_id' => 'required|exists:products,id,deleted,0,status,active',
                'product_variation_id' => 'required|numeric',
                'count' => 'required|integer'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        if($request->product_variation_id)
        {
            $variation = ProductVariation::where('id',$request->product_variation_id)->select('id','product_id','sale_price','count')->first();

            if(! $variation) r_json(['msg' => word('invalid_product_variation_id')],401);
            if($variation->count < $request->count) return r_json(['msg' => word('insufficient_product_count')],401);

            $price = $variation->sale_price * $request->count;
        }
        else
        {
            $product = Product::where('id',$request->product_id)->select('id','price_meta')->first();
            if($product->price_meta->count < $request->count) return r_json(['msg' => word('insufficient_product_count')],401);

            $price = $product->price_meta->sale_price * $request->count;
        }

        $this_product = Product::where('id',$request->product_id)->select('country_id','seller_id')->first();

        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        if($this_product->country_id != $country_id) return r_json(['msg' => word('product_not_same_country_as_user')],401);

        Cart::updateOrCreate
        (
            [
                'user_id' => $request->user_id,
                'seller_id' => $this_product->seller_id,
                'product_id' => $request->product_id
            ],
            [
                'product_variation_id' => $request->product_variation_id,
                'count' => $request->count,
                'price' => $price
            ]
        );

        return r_json([],204);
    }


    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'address_id' => 'required|exists:addresses,id,deleted,0,user_id,'.$request->user_id
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }


        $carts = Cart::where('user_id',$request->user_id)->select('id','seller_id','product_id','product_variation_id','count')->get()->groupBy('seller_id');

        foreach($carts as $seller_id => $cart)
        {
            $cart_total = 0;
            $products = [];
            foreach($cart as $this_cart)
            {
                $arr['product_id'] = $this_cart->product_id;
                $arr['product_variation_id'] = $this_cart->product_variation_id;
                $arr['count'] = $this_cart->count;

                $product = Product::where('id',$this_cart->product_id)->select('id','type','price_meta')->first();
                if($product->type == 'static')
                {
                    if($this_cart->count > $product->price_meta->count) return r_json(['msg' => word('insufficient_product_count')],401);
                    $arr['price'] = $product->price_meta->price * $this_cart->count;

                    $product->price_meta->count -= $arr['count'];
                    $product->sold ++;
                    $product->save();
                }
                else
                {
                    $variation = ProductVariation::where('product_id',$this_cart->product_id)->where('id',$this_cart->product_variation_id)->select('id','sale_price','count')->first();
                    if($this_cart->count > $variation->count) return r_json(['msg' => word('insufficient_product_count')],401);
                    $arr['price'] = $variation->sale_price * $this_cart->count;

                    $variation->count -= $arr['count'];
                    $product->sold ++;
                    $variation->save();
                }

                $products[] = $arr;
                $cart_total += $arr['price'];
            }

            $totals['items'] = $cart_total;
            $totals['tax'] = User::getTaxPercentage($request->user_id,$cart_total);
            $totals['total'] = $totals['items'] + $totals['tax'];

            $order = Order::create
            (
                [
                    'country_id' => User::where('id',$request->user_id)->select('country_id')->first()->country_id,
                    'status' => 'pending',
                    'code' => get_rand(10),
                    'seller_id' => $seller_id,
                    'buyer_id' => $request->user_id,
                    'address_id' => $request->address_id,
                    'items' => json_encode($products),
                    'items_count' => $cart->count(),
                    'items_fee' => $totals['items'],
                    'tax_fee' => $totals['tax'],
                    'total_fee' => $totals['total'],
                ]
            );

            $ar_text = 'لديك طلب جديد #'.$order->code;
            $en_text = 'You got a new order #'.$order->code;

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
        }

        Cart::where('user_id',$request->user_id)->delete();

        return r_json([],204);
    }


    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'cart_id' => 'required|exists:carts,id,user_id,'.$request->user_id,
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        Cart::find($request->cart_id)->delete();

        return r_json([],204);
    }
}
