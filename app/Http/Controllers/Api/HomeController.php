<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Slide;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        $sliders = Slide::whereIn('country_id',[0,$country_id])->where('expire_at','>=',Carbon::today()->toDateString())->where('status','active')->select('url','image_mobile as image')->get();
        $categories = Category::where('type','main')->where('status','active')->select('id','type',lang().'_name as name','image')->take(5)->get();
        $best_selling = Product::where('country_id',$country_id)->where('status','active')->orderBy('sold','desc')->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->take(9)->get();
        $most_viewed = Product::where('country_id',$country_id)->where('status','active')->orderBy('views','desc')->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->take(9)->get();
        $discounts = Product::where('country_id',$country_id)->where('status','active')->where('discount',1)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->inRandomOrder()->take(9)->get();

        foreach($best_selling as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        foreach($most_viewed as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        foreach($discounts as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        return r_json(['sliders' => $sliders,'categories' => $categories,'best_selling' => $best_selling,'most_viewed' => $most_viewed,'discount' => $discounts]);
    }


    public function search(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|numeric',
                'text' => 'required'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        $products = Product::where('status','active')->where('country_id',$country_id)->where(function($q) use($request)
        {
            $check = preg_match('@[a-zA-Z]@', $request->text);
            if($check) $q->where('en_name','like','%'.$request->text.'%');
            else $q->orWhere('ar_name','like','%'.$request->text.'%');
        })->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->paginate(20);

        foreach($products as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        return r_json($products);
    }


    public function categories(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'parent' => 'required|numeric',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        if($request->parent != 0) $categories = Category::where('parent_id',$request->parent)->where('status','active')->select('id',lang().'_name as name','image')->get();
        else $categories = Category::where('type','main')->where('status','active')->select('id',lang().'_name as name','image')->get();

        foreach($categories as $category) $category['subs'] = Category::where('parent_id',$category->id)->where('status','active')->exists();

        return r_json($categories);
    }


    public function category_products(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
                'cat_id' => 'required|exists:categories,id,status,active,deleted,0,type,sec',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        $products = Product::where('country_id',$country_id)->where('status','active')->where('sec_cat_id',$request->cat_id)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->paginate(20);

        foreach($products as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        return r_json($products);
    }


    public function all_products(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
                'type' => 'required|in:best_selling,most_viewed,discount',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('id',$request->user_id)->select('country_id')->first();
        $country_id = $user ? Country::where('id',$user->country_id)->select('id')->first()->id : 1;

        if($request->type == 'discount') $products = Product::where('discount',1);
        else
        {
            if($request->type == 'best_selling') $products = Product::orderBy('sold','desc');
            elseif($request->type == 'most_viewed') $products = Product::orderBy('views','desc');
        }

        $products = $products->where('country_id',$country_id)->where('status','active')->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->paginate(20);

        foreach($products as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        return r_json($products);
    }
}
