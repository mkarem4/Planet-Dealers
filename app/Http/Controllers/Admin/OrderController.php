<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');
        $orders = Order::where(function($q) use($inputs)
        {
            if(isset($inputs['code'])) $q->where('code','like','%'.$inputs['code'].'%');
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
            if(isset($inputs['user']) && $inputs['user'] != 'all') $q->where('seller_id',$inputs['user'])->orWhere('buyer_id',$inputs['user']);
        })->latest()->paginate(20)->appends($inputs);

        $users = User::select('id','first_name','last_name')->get();

        $pending_count = Order::where('status','pending')->count();
        $confirmed_count = Order::where('status','confirmed')->count();
        $processing_count = Order::where('status','processing')->count();
        $delivered_count = Order::where('status','delivered')->count();
        $canceled_count = Order::where('status','canceled')->count();

        return view('admin.orders.index', get_defined_vars());
    }


    public function show($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'exists:orders,id'
            ]
        );

        $order = Order::find($id);

        $buyer = User::where('id',$order->buyer_id)->select('first_name','last_name','email','phone')->first();
        $buyer['address'] = Address::where('id',$order->address_id)->select('text')->first();
        $seller = User::where('id',$order->buyer_id)->select('first_name','last_name','email','phone')->first();

        $items = new Collection();

        foreach($order->items as $item)
        {

            $product = Product::where('id',$item->product_id)->select('id','type',lang().'_name as name','thumb_image as image')->first();
            if($product->type == 'variable')
            {
                $variation = ProductVariation::where('product_id',$item->product_id)->where('id',$item->product_variation_id)->select('options','price','sale_price')->first();

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

            $product['pricing'] = $arr;

            $items->push($product);

        }

        $order['items'] = $items;

        return view('admin.orders.show', compact('order','buyer','seller'));
    }


    public function cancel(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:orders,id',
                'admin_notes' => 'required',
            ],
            [
                'admin_notes.required' => 'field_required'
            ]
        );

        Order::where('id',$request->id)->update(['status' => 'canceled','admin_notes' => $request->notes]);

        return back()->with('success', 'updated');
    }
}
