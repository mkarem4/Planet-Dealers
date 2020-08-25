<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');
        $products = Product::where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where('en_name','like','%'.$inputs['name'].'%');
                else $q->where('ar_search','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
            if(isset($inputs['featured']) && $inputs['featured'] != 'all') $q->where('featured',$inputs['featured']);
            if(isset($inputs['discount']) && $inputs['discount'] != 'all')
            {
                $ids = ProductVariation::where('sale',1)->pluck('product_id');
                $q->whereIn('id',$ids);
            }
            if(isset($inputs['type']) && $inputs['type'] != 'all') $q->where('type',$inputs['type']);
        })->latest()->select('id','country_id','status','main_cat_id','sub_cat_id','sec_cat_id','seller_id',lang().'_name as name','image','thumb_image','price_meta','rate','rate_count','type','discount','discount_till','featured','featured_till','created_at')->paginate(20)->appends($inputs);

        foreach($products as $product)
        {
            if($product->type == 'variable')
            {
                $product['variations_data'] = ProductVariation::getVariationsOptionsApi($product->id);
            }
            $product['seller'] = User::where('id',$product->seller_id)->select('id','city_id','first_name','last_name','company_name','email','phone')->first();
        }

        $active_count = Product::where('status','active')->count();
        $suspended_count = Product::where('status','suspended')->count();
        $on_hold_count = Product::where('status','on_hold')->count();
        $deleted_count = Product::withoutGlobalScopes()->where('deleted','1')->count();

        return view('admin.products.index', get_defined_vars());
    }


    public function feature_handle(Request $request)
    {

        $this->validate($request,
            [
                'id' => 'required|exists:products,id',
                'featured' => 'required|in:0,1',
                'featured_till' => 'sometimes|nullable|date',
            ],
            [
                'featured.required' => 'field_required',
                'featured.in' => 'field_invalid',
                'featured_till.date' => 'field_invalid'
            ]
        );

        $product = Product::find($request->id);
            $product->featured = $request->featured;
            $product->featured_till = $request->featured_till;
        $product->save();

        return back()->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        Product::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id',
            ]
        );

        Product::where('id',$request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
