<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductMeta;
use App\Models\ProductRate;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('seller_id',user()->id)->select('id','status','sec_cat_id',lang().'_name as name','price_meta')->paginate();
        return view('web/products/index',compact('products'));
    }


    public function create()
    {
        $categories = Category::where('type','main')->select('id',lang().'_name as name')->get();
        $cities = Country::where('parent_id',user()->country_id)->select('id',lang().'_name as name')->get();
        $variations = Variation::where('status','active')->select('id',lang().'_name as name')->get();

        return view('web.products.single',compact('categories','cities','variations'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'city_id' => 'required|exists:countries,id,type,sub,status,active,deleted,0',
                'sec_cat_id' => 'required|exists:categories,id,type,sec,status,active,deleted,0',
                'ar_name' => 'required',
                'en_name' => 'required',
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'ar_custom' => 'sometimes',
                'en_custom' => 'sometimes',
                'image' => 'required|image|mimes:jpg,jpeg,png,tif,gif,webp',
                'images' => 'sometimes|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,tif,gif,webp',
                'type' => 'required|in:static,variable',
                'options' => [
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->type == 'variable') if (!$request->options) $fail('pricing_required');

                        $variation_options = $request->options;
                        foreach ($variation_options as $key => $option) {
                            if (!array_key_exists('ids', $option) || !array_key_exists('price', $option) || !array_key_exists('count', $option)) unset($variation_options[$key]);
                            if (!is_array($option['ids'])) $fail('pricing_error');
                            foreach ($option['ids'] as $id) {
                                if (VariationOption::where('status', 'active')->where('id', $id)->exists() == false) $fail('pricing_not_exists');
                            }
                            if (!is_numeric($option['price']) || !is_numeric($option['count'])) $fail('pricing_not_exists2');
                        }
                        if (count($variation_options) == 0) $fail('pricing_required');
                    }
                ],
            ],
            [
                'city_id.required' => 'field_required',
                'city_id.exists' => 'field_invalid',
                'sec_cat_id.required' => 'field_required',
                'sec_cat_id.exists' => 'field_invalid',
                'ar_name.required' => 'field_required',
                'eb_name.required' => 'field_required',
                'ar_desc.required' => 'field_required',
                'en_desc.required' => 'field_required',
                'image.required' => 'field_required',
                'image.image' => 'image_invalid',
                'image.mimes' => 'image_invalid',
                'images.array' => 'image_invalid',
                'images.*.image' => 'image_invalid',
                'images.*.mimes' => 'image_invalid',
            ]
        );

        $sub_cat_id = Category::where('id', $request->sec_cat_id)->select('parent_id')->first()->parent_id;
        $main_cat_id = Category::where('id', $sub_cat_id)->select('parent_id')->first()->parent_id;

        $product = new Product();
        $product->type = $request->type;
        try {
            if ($request->type == 'static')
            {
                $sale_price = $request->sale_price ? $request->sale_price : $request->price;
                $sale = $request->sale_till ? 1 : 0;

                $arr['price'] = $request->price;
                $arr['sale_price'] = $sale_price;
                $arr['sale_till'] = $request->sale_till;
                $arr['sale'] = $sale;
                $arr['count'] = $request->count;

                $product->price_meta = json_encode($arr);
                $product->discount = $sale;
                $product->discount_till = $request->sale_till;
            }
            else
            {
                $variation_options = $request->options;
                foreach ($variation_options as $key => $option)
                {
                    if (!array_key_exists('ids', $option) || !array_key_exists('price', $option) || !array_key_exists('count', $option)) unset($variation_options[$key]);
                }

                $array_0_index = $variation_options[array_key_first($variation_options)];

                $sale_price = array_key_exists('sale_price',$array_0_index) && $option['sale_price'] ? $array_0_index['sale_price'] : $array_0_index['price'];
                $sale = array_key_exists('sale_till',$array_0_index) ? 1 : 0;
                $sale_till = array_key_exists('sale_till',$array_0_index) ? $array_0_index['sale_till'] : NULL;

                $arr['price'] = $array_0_index['price'];
                $arr['sale_price'] = $sale_price;
                $arr['sale_till'] = $sale_till;
                $arr['sale'] = $sale;
                $arr['count'] = $array_0_index['count'];

                $product->price_meta = json_encode($arr);
                $product->discount = $sale;
                $product->discount_till = $array_0_index['sale_till'];
            }
        }
        catch (\Exception $exception)
        {
            return back()->with('error', word('problem_with_pricing'));
        }

        $product->status = 'active';
        $product->seller_id = user()->id;
        $product->country_id = user()->country_id;
        $product->city_id = $request->city_id;
        $product->main_cat_id = $main_cat_id;
        $product->sub_cat_id = $sub_cat_id;
        $product->sec_cat_id = $request->sec_cat_id;
        $product->ar_name = $request->ar_name;
        $product->en_name = $request->en_name;
        $product->ar_search = ar_search($request->ar_name);

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/products/' . $info['month']), $info['image']);

        $image = Image::make(public_path('/uploads/products/' . $info['name']));
        $image->resize(470, 430);
        $image->save(public_path('/uploads/products/' . $info['name']));

        $image = Image::make(public_path('/uploads/products/' . $info['name']));
        $image->resize(163, 150);
        $image->save(public_path('/uploads/products/' . $info['thumb_name']));

        $product->image = $info['name'];
        $product->thumb_image = $info['thumb_name'];
        $product->save();

        if ($product->type == 'variable' && isset($variation_options))
        {
            foreach ($variation_options as $key => $option)
            {
                $sale_price = array_key_exists('sale_price',$option) && $option['sale_price'] ? $option['sale_price'] : $option['price'];
                $sale = array_key_exists('sale_till',$option) ? 1 : 0;
                $sale_till = array_key_exists('sale_till',$option) ? $option['sale_till'] : NULL;

                ProductVariation::create
                (
                    [
                        'product_id' => $product->id,
                        'options' => json_encode($option['ids']),
                        'price' => $option['price'],
                        'sale_price' => $sale_price,
                        'sale_till' => $sale_till,
                        'sale' => $sale,
                        'count' => $option['count'],
                    ]
                );
            }
        }

        ProductMeta::create(['product_id' => $product->id, 'key' => 'ar_desc', 'value' => $request->ar_desc]);
        ProductMeta::create(['product_id' => $product->id, 'key' => 'en_desc', 'value' => $request->en_desc]);
        if($request->ar_custom) ProductMeta::create(['product_id' => $product->id, 'key' => 'ar_custom', 'value' => $request->ar_custom]);
        if($request->ar_custom) ProductMeta::create(['product_id' => $product->id, 'key' => 'en_custom', 'value' => $request->en_custom]);


//        return $request->images ;

        if ($request->images){
            foreach($request->images as $image)
            {
                $info = unique_file_folder($image->getClientOriginalExtension());
                $image->move(public_path('/uploads/products/'.$info['month']),$info['image']);

                $this_image = Image::make(public_path('/uploads/products/'.$info['name']));
                $this_image->resize(470, 430);
                $this_image->save(public_path('/uploads/products/'.$info['name']));

                ProductMeta::create(['product_id' => $product->id, 'key' => 'image', 'value' => $info['name']]);
            }
        }


        return redirect('/profile/products')->with('success','created');
    }


    public function show($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0,status,active'
            ]
        );

        $product = Product::where('id',$id)->select('id','main_cat_id','sub_cat_id','sec_cat_id','seller_id',lang().'_name as name','image','thumb_image','rate','rate_count','type','price_meta','discount')->first();
            $product->views ++;
        $product->save();

        if($product->type == 'variable')
        {
            $product['variations'] = ProductVariation::getVariationsWeb($product->id);
            $product['variations_data'] = ProductVariation::getVariationsOptionsApi($product->id);
        }

        $product['desc'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_desc')->select('value')->first()->value;
        $product['custom'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->exists() ? ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->select('value')->first()->value : '';
        $product['images'] = ProductMeta::where('product_id',$product->id)->where('key','image')->select('value as image')->pluck('image');
        $product['is_favorite'] = user() ? Favorite::where('user_id',user()->id)->where('product_id',$product->id)->exists() : false;
        $product['is_cart'] = user() ? Cart::where('user_id',user()->id)->where('product_id',$product->id)->exists() : false;

        $rates = ProductRate::where('product_id',$product->id)->select('user_id','text','rate','created_at')->paginate(1);
        foreach($rates as $rate) $rate['user'] = User::where('id',$rate->user_id)->select('first_name','last_name','image')->first();

        $product['rates'] = $rates;

        $seller = User::where('id',$product->seller_id)->select('id','country_id','first_name','last_name','company_name','image')->first();
        $seller['country'] = Country::where('id',$seller->country_id)->select(lang().'_name as name')->first();

        $similars = Product::where('sec_cat_id',$product->sec_cat_id)->where('id','!=',$product->id)->where('status','active')->inRandomOrder()->select('id',lang().'_name as name','thumb_image as image','price_meta')->take(2)->get();
        foreach($similars as $similar)
        {
            $similar['is_favorite'] = user() ? Favorite::where('user_id',user()->id)->where('product_id',$similar->id)->exists() : false;
            $similar['is_cart'] = user() ? Cart::where('user_id',user()->id)->where('product_id',$similar->id)->exists() : false;
        }

        return view('web.products.show',get_defined_vars());
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0,seller_id,'.user()->id
            ]
        );

        $product = Product::find($id);
        $categories = Category::where('type','main')->select('id',lang().'_name as name')->get();
        $cities = Country::where('parent_id',user()->country_id)->select('id',lang().'_name as name')->get();
        $variations = Variation::where('status','active')->select('id',lang().'_name as name')->get();

        if($product->type == 'variable')
        {
            $product['variations'] = ProductVariation::getVariationsWeb($product->id);
            $product['variations_data'] = ProductVariation::getVariationsOptionsWeb($product->id);
        }

        return view('web.products.single',get_defined_vars());
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0,seller_id,'.user()->id,
                'city_id' => 'required|exists:countries,id,type,sub,status,active,deleted,0',
                'sec_cat_id' => 'required|exists:categories,id,type,sec,status,active,deleted,0',
                'ar_name' => 'required',
                'en_name' => 'required',
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'ar_custom' => 'sometimes',
                'en_custom' => 'sometimes',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png,tif,gif,webp',
                'images' => 'sometimes|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,tif,gif,webp',
                'type' => 'required|in:static,variable',
                'options' => [
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->type == 'variable') if (!$request->options) $fail('pricing_required');

                        $variation_options = $request->options;
                        foreach ($variation_options as $key => $option) {
                            if (!array_key_exists('ids', $option) || !array_key_exists('price', $option) || !array_key_exists('count', $option)) unset($variation_options[$key]);
                            if (!is_array($option['ids'])) $fail('pricing_error');
                            foreach ($option['ids'] as $id) {
                                if (VariationOption::where('status', 'active')->where('id', $id)->exists() == false) $fail('pricing_not_exists');
                            }
                            if (!is_numeric($option['price']) || !is_numeric($option['count'])) $fail('pricing_not_exists2');
                        }
                        if (count($variation_options) == 0) $fail('pricing_required');
                    }
                ],
            ],
            [
                'city_id.required' => 'field_required',
                'city_id.exists' => 'field_invalid',
                'sec_cat_id.required' => 'field_required',
                'sec_cat_id.exists' => 'field_invalid',
                'ar_name.required' => 'field_required',
                'eb_name.required' => 'field_required',
                'ar_desc.required' => 'field_required',
                'en_desc.required' => 'field_required',
                'image.required' => 'field_required',
                'image.image' => 'image_invalid',
                'image.mimes' => 'image_invalid',
                'images.array' => 'image_invalid',
                'images.*.image' => 'image_invalid',
                'images.*.mimes' => 'image_invalid',
            ]
        );

        $sub_cat_id = Category::where('id', $request->sec_cat_id)->select('parent_id')->first()->parent_id;
        $main_cat_id = Category::where('id', $sub_cat_id)->select('parent_id')->first()->parent_id;

        $product = Product::find($request->id);
        $product->type = $request->type;
        try
        {
            if ($request->type == 'static')
            {
                $sale_price = $request->sale_price ? $request->sale_price : $request->price;
                $sale = $request->sale_till ? 1 : 0;

                $arr['price'] = $request->price;
                $arr['sale_price'] = $sale_price;
                $arr['sale_till'] = $request->sale_till;
                $arr['sale'] = $sale;
                $arr['count'] = $request->count;

                $product->price_meta = json_encode($arr);
                $product->discount = $sale;
                $product->discount_till = $request->sale_till;
            }
            else
            {
                $variation_options = $request->options;
                foreach ($variation_options as $key => $option)
                {
                    if (!array_key_exists('ids', $option) || !array_key_exists('price', $option) || !array_key_exists('count', $option)) unset($variation_options[$key]);
                }

                $array_0_index = $variation_options[array_key_first($variation_options)];

                $sale_price = array_key_exists('sale_price',$array_0_index) && $option['sale_price'] ? $array_0_index['sale_price'] : $array_0_index['price'];
                $sale = array_key_exists('sale_till',$array_0_index) ? 1 : 0;
                $sale_till = array_key_exists('sale_till',$array_0_index) ? $array_0_index['sale_till'] : NULL;

                $arr['price'] = $array_0_index['price'];
                $arr['sale_price'] = $sale_price;
                $arr['sale_till'] = $sale_till;
                $arr['sale'] = $sale;
                $arr['count'] = $array_0_index['count'];

                $product->price_meta = json_encode($arr);
                $product->discount = $sale;
                $product->discount_till = $sale_till;
            }
        }
        catch (\Exception $exception)
        {
            return back()->with('error', word('problem_with_pricing'));
        }

        $product->city_id = $request->city_id;
        $product->main_cat_id = $main_cat_id;
        $product->sub_cat_id = $sub_cat_id;
        $product->sec_cat_id = $request->sec_cat_id;
        $product->ar_name = $request->ar_name;
        $product->en_name = $request->en_name;
        $product->ar_search = ar_search($request->ar_name);

        if($request->image)
        {
            $info = unique_file_folder($request->image->getClientOriginalExtension());
            $request->image->move(public_path('/uploads/products/' . $info['month']), $info['image']);

            $image = Image::make(public_path('/uploads/products/' . $info['name']));
            $image->resize(470, 430);
            $image->save(public_path('/uploads/products/' . $info['name']));

            $image = Image::make(public_path('/uploads/products/' . $info['name']));
            $image->resize(163, 150);
            $image->save(public_path('/uploads/products/' . $info['thumb_name']));

            @unlink(public_path('/uploads/products/').$product->getOriginal('image'));
            @unlink(public_path('/uploads/products/').$product->getOriginal('thumb_image'));

            $product->image = $info['name'];
            $product->thumb_image = $info['thumb_name'];
        }

        $product->save();

        if ($product->type == 'variable' && isset($variation_options))
        {
            $existed_ids = [];

            foreach ($variation_options as $key => $option)
            {

                $sale_price = array_key_exists('sale_price',$option) && $option['sale_price'] ? $option['sale_price'] : $option['price'];
                $sale = array_key_exists('sale_till',$option) ? 1 : 0;
                $sale_till = array_key_exists('sale_till',$option) ? $option['sale_till'] : NULL;

                $check = ProductVariation::where('product_id',$product->id)->where(function($q) use($request,$option)
                {
                    foreach($option['ids'] as $id)
                    {
                        $q->whereJsonContains('options',$id);
                    }

                    $q->whereJsonLength('options',count($option['ids']));

                })->select('id')->first();

                if($check)
                {
                    $existed_ids[] = $check->id;

                    ProductVariation::find($check->id)->update
                    (
                        [
                            'options' => json_encode($option['ids']),
                            'price' => $option['price'],
                            'sale_price' => $sale_price,
                            'sale_till' => $sale_till,
                            'sale' => $sale,
                            'count' => $option['count'],
                        ]
                    );
                }
                else
                {
                    $variation = ProductVariation::create
                    (
                        [
                            'product_id' => $product->id,
                            'options' => json_encode($option['ids']),
                            'price' => $option['price'],
                            'sale_price' => $sale_price,
                            'sale_till' => $sale_till,
                            'sale' => $sale,
                            'count' => $option['count'],
                        ]
                    );

                    $existed_ids[] = $variation->id;
                }
            }


            ProductVariation::where('product_id',$product->id)->whereNotIn('id',$existed_ids)->update(['deleted' => 1]);
        }

        ProductMeta::updateOrCreate(['product_id' => $product->id, 'key' => 'ar_desc'],['value' => $request->ar_desc]);
        ProductMeta::updateOrCreate(['product_id' => $product->id, 'key' => 'en_desc'],['value' => $request->en_desc]);

        if($request->ar_custom) ProductMeta::updateOrCreate(['product_id' => $product->id, 'key' => 'ar_custom'],['value' => $request->ar_custom]);
        if($request->en_custom) ProductMeta::updateOrCreate(['product_id' => $product->id, 'key' => 'en_custom'],['value' => $request->en_custom]);

        if($request->images)
        {
            foreach($request->images as $image)
            {
                $info = unique_file_folder($image->getClientOriginalExtension());
                $image->move(public_path('/uploads/products/'.$info['month']),$info['image']);

                $this_image = Image::make(public_path('/uploads/products/'.$info['name']));
                $this_image->resize(470, 430);
                $this_image->save(public_path('/uploads/products/'.$info['name']));

                ProductMeta::create(['product_id' => $product->id, 'key' => 'image', 'value' => $info['name']]);
            }
        }

        return redirect('/profile/products')->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Product::find($request->id)->update(['status' => $request->status]);
        Cart::where('product_id',$request->id)->delete();

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0',
            ]
        );

        Product::where('id',$request->id)->update(['deleted' => 1]);
        Cart::where('product_id',$request->id)->delete();

        return back()->with('success', 'deleted');
    }



    public function get_variations_options(Request $request)
    {
        $this->validate($request,
            [
                'variation_ids' => 'required|array',
                'variation_ids.*' => 'exists:variations,id,deleted,0,status,active'
            ]
        );

        $variations = Variation::whereIn('id',$request->variation_ids)->select('id',lang().'_name as name')->get();
        foreach($variations as $variation) $variation['options'] = VariationOption::where('parent_id',$variation->id)->select('id',lang().'_name as name')->get();

        return r_json($variations);
    }


    public function seller_profile($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:users,id,type,seller,status,active,deleted,0,country_id,'.country()->id
            ]
        );

        $user = User::where('id',$id)->select('id','country_id','city_id','company_name','first_name','last_name','image','email','phone','whatsapp')->first();
        $products = Product::where('seller_id',$user->id)->latest()->select('id','main_cat_id',lang().'_name as name','thumb_image as image','price_meta')->paginate(12);

        foreach($products as $product)
        {
            $product['is_favorite'] = user() ? Favorite::where('user_id',user()->id)->where('product_id',$product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id',user()->id)->where('product_id',$product->id)->exists() : false;
        }

        return view('web.profile.seller_profile', compact('user','products'));
    }


    public function rate(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,deleted,0',
                'rating' => 'required|in:1,2,3,4,5',
                'text' => 'required'
            ],
            [
                'rating.required' => 'rate_required',
                'text.required' => 'text_required',
            ]
        );

        $product = Product::where('id',$request->id)->select('id','seller_id','rate','rate_count')->first();
        if($product->seller_id == user()->id) return back()->with('error','rate_not_allowed');

        ProductRate::updateOrcreate
        (
            [
                'product_id' => $request->id,
                'user_id' => user()->id,
            ]
            ,[
                'rate' => $request->rating,
                'text' => $request->text,
            ]
        );

        $product->rate = ProductRate::where('product_id',$product->id)->avg('rate');
        $product->rate_count += 1;
        $product->save();

        return back()->with('success','submitted');
    }
}
