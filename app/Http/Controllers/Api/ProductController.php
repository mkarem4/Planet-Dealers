<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductMeta;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
                'product_id' => 'required|exists:products,id,deleted,0,status,active',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $product = Product::where('id',$request->product_id)->select('id','country_id','sec_cat_id as category','seller_id',lang().'_name as name','image','rate','type','price_meta')->first();
            $product->views ++;
        $product->save();


        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        if($product->country_id != $country_id) return r_json(['msg' => word('product_not_same_country_as_user')],401);

        unset($product->country_id,$product->views,$product->updated_at);

        if($product->type == 'variable')
        {
            $product['variations'] = ProductVariation::getVariations($product->id);
            $product['variations_data'] = ProductVariation::getVariationsOptionsApi($product->id);
        }
        else
        {
            $product['variations'] = [];
            $product['variations_data'] = [];
        }

        $product['category'] = Category::where('id',$product->category)->select('id',lang().'_name as name')->first();
        $product['desc'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_desc')->select('value')->first()->value;
        $product['custom'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->exists() ? ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->select('value')->first()->value : '';
        $product['images'] = ProductMeta::where('product_id',$product->id)->where('key','image')->select('value as image')->get();
        $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();


        $seller = User::where('id',$product->seller_id)->select('id','country_id','first_name','last_name','image')->first();
        $seller['country'] = Country::where('id',$seller->country_id)->select(lang().'_name as name')->first();

        $similars = Product::where('sec_cat_id',$product->category->id)->where('id','!=',$product->id)->where('status','active')->inRandomOrder()->select('id',lang().'_name as name','thumb_image as image','price_meta')->take(2)->get();
        foreach($similars as $similar)
        {
            $similar['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$similar->id)->exists();
            $similar['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$similar->id)->exists();
        }

        return r_json(['product' => $product,'seller' => $seller,'similars' => $similars]);
    }
}
