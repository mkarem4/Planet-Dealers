<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
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
    public function index()
    {
        $carts = Cart::where('user_id',user()->id)->select('id','product_id','product_variation_id','count','price')->paginate(21);
        foreach($carts as $cart)
        {
            $product = Product::where('id',$cart->product_id)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->first();
            if($product->type == 'variable')
            {
                $product['price_meta'] = ProductVariation::where('id',$cart->product_variation_id)->select('price','sale_price','count')->first();
            }

            $cart['product'] = $product;
        }



        $totals['cart'] = round(Cart::where('user_id',user()->id)->sum('price'),2);
        $totals['tax'] = round(User::getTaxPercentage(user()->id,$totals['cart']),2);
        $totals['total'] = $totals['cart'] + $totals['tax'];

        return view('web.cart.index', get_defined_vars());
    }


    public function store(Request $request)
    {

        $this->validate($request,
            [
                'product_id' => 'required|exists:products,id,deleted,0,status,active',
                'product_variation_id' => 'required|numeric',
                'count' => 'required|integer'
            ],
            [
                'count.required' => 'count_required',
                'count.integer' => 'count_invalid',
            ]
        );


        if($request->product_variation_id)
        {
            $variation = ProductVariation::where('id',$request->product_variation_id)->select('id','product_id','sale_price','count')->first();

            if(! $variation) back()->with('error',word('invalid_product_variation_id'));
            if($variation->count < $request->count) return back()->with('error',word('insufficient_product_count'));

            $price = $variation->sale_price * $request->count;
        }
        else
        {
            $product = Product::where('id',$request->product_id)->select('id','price_meta')->first();
            if($product->price_meta->count < $request->count) return back()->with('error',word('insufficient_product_count'));

            $price = $product->price_meta->sale_price * $request->count;
        }

        $seller_id = Product::where('id',$request->product_id)->select('seller_id')->first()->seller_id;

        Cart::updateOrCreate
        (
            [
                'user_id' => user()->id,
                'seller_id' => $seller_id,
                'product_id' => $request->product_id
            ],
            [
                'product_variation_id' => $request->product_variation_id,
                'count' => $request->count,
                'price' => $price
            ]
        );

        return back()->with('success','carted');
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'cart' => 'required'
            ]
        );

        foreach($request->cart as $key => $value)
        {

            $cart = Cart::where('id',$key)->where('user_id',user()->id)->first();
            if(! $cart) return back()->with('error','invalid_cart_item');

            if($cart->product_variation_id) $price_meta = ProductVariation::where('id',$cart->product_variation_id)->where('product_id',$cart->product_id)->select('sale_price','count')->first();
            else $price_meta = Product::where('id',$cart->product_id)->select('price_meta')->first()->price_meta;

            if($key > $price_meta->count) return back()->with('error','invalid_cart_count');

            $cart->update
            (
                [
                    'count' => $value,
                    'price' => $price_meta->sale_price * $value
                ]
            );
        }

        return back()->with('success','updated');
    }


    public function checkout(Request $request)
    {
        $this->validate($request,
            [
                'address_id' => 'required|exists:addresses,id,user_id,'.user()->id
            ],
            [
                'address_id.required' => 'field_required',
                'address_id.exists' => 'field_invalid'
            ]
        );

        $carts = Cart::where('user_id',user()->id)->select('id','seller_id','product_id','product_variation_id','count')->get()->groupBy('seller_id');

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
            $totals['tax'] = User::getTaxPercentage(user()->id,$cart_total);
            $totals['total'] = $totals['items'] + $totals['tax'];

            $order = Order::create
            (
                [
                    'country_id' => user()->country_id,
                    'status' => 'pending',
                    'code' => get_rand(10),
                    'seller_id' => $seller_id,
                    'buyer_id' => user()->id,
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

        Cart::where('user_id',user()->id)->delete();

        return redirect('/profile/orders')->with('success','created');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'cart_id' => 'required|exists:carts,id,user_id,'.user()->id,
            ]
        );

        Cart::find($request->cart_id)->delete();

        $cart = Cart::where('user_id',user()->id)->select('price')->get();

        $count = $cart->count();
        $total = $cart->sum('price') . ' ' . country()->currency;

        return r_json(['status' => 'success','count' => $count,'total' => $total]);
    }
}
