<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\Newsletter;
use App\Models\Product;
use App\Models\SearchRequest;
use App\Models\Slide;
use App\Models\Social;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::whereIn('country_id', [0, country()->id])->where('expire_at', '>=', Carbon::today()->toDateString())->where('status', 'active')->select('url', 'image')->get();
        return view('web.index', get_defined_vars());
    }


    public function products()
    {
        $inputs = request()->except('page');
        $products = Product::where('status', 'active')->where(function ($q) use ($inputs) {
            if (user()) $q->where('city_id', user()->city_id);
            if (isset($inputs['name'])) {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if ($check) $q->where('en_name', 'like', '%' . $inputs['name'] . '%');
                else $q->where('ar_name', 'like', '%' . $inputs['name'] . '%');
            }
            if (isset($inputs['category']) && $inputs['category'] != 'all') $q->where('main_cat_id', $inputs['category'])->orWhere('sub_cat_id', $inputs['category'])->orWhere('sec_cat_id', $inputs['category']);
            if (isset($inputs['order_by'])) {
                if ($inputs['order_by'] == 'featured') $q->where('featured', 1);
                if ($inputs['order_by'] == 'discount') $q->where('discount', 1);
            }
        });

        if (isset($inputs['order_by'])) {
            if ($inputs['order_by'] == 'best_selling') $products = $products->orderBy('sold', 'desc');
            if ($inputs['order_by'] == 'views') $products = $products->orderBy('views', 'desc');
            if ($inputs['order_by'] == 'rating') $products = $products->orderBy('rate', 'desc');
            if ($inputs['order_by'] == 'latest') $products = $products->orderBy('created_at', 'desc');
            if ($inputs['order_by'] == 'oldest') $products = $products->orderBy('created_at', 'asc');
            if ($inputs['order_by'] == 'price_desc') $products = $products->orderBy('price_meta->sale_price', 'desc');
            if ($inputs['order_by'] == 'price_asc') $products = $products->orderBy('price_meta->sale_price', 'asc');
        }

        $products = $products->select('id', 'main_cat_id', lang() . '_name as name', 'image', 'price_meta');
        if (isset($inputs['show'])) {
            if ($inputs['show'] != 'all' && intval($inputs['show'])) $products = $products->paginate($inputs['show']);
            elseif ($inputs['show'] == 'all') $products = $products->get();
        } else $products = $products->paginate(21);


        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return view('web.all_products', get_defined_vars());
    }


    public function change_country($id, Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,type,main,status,active,deleted,0'
            ]
        );

        Session::put('country_id', $request->id);

        return back();
    }


    public static function getBanner()
    {
        return Banner::whereIn('country_id', [0, country()->id])->where('status', 'active')->where('expire_at', '>', Carbon::today()->toDateString())->inRandomOrder()->first();
    }


    public static function getFeatured($take)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->where('featured', 1)->select('id', 'main_cat_id', lang() . '_name as name', 'image', 'thumb_image', 'price_meta')->inRandomOrder()->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getDiscount($take, $featured = 0)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->where('discount', 1)->where('featured', 1)->select('id', 'main_cat_id', lang() . '_name as name', 'thumb_image as image', 'price_meta', 'sold', 'discount_till')->inRandomOrder()->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getTopRated($take)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->orderBy('rate', 'desc')->select('id', 'main_cat_id', lang() . '_name as name', 'thumb_image as image', 'price_meta')->inRandomOrder()->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getBestSelling($take)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->orderBy('sold', 'desc')->select('id', 'main_cat_id', lang() . '_name as name', 'thumb_image as image', 'price_meta')->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getMostViewed($take)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->orderBy('views', 'desc')->select('id', 'main_cat_id', lang() . '_name as name', 'thumb_image as image', 'price_meta')->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getRecentlyAdded($take)
    {
        $products = Product::where('country_id', country()->id)->where('status', 'active')->orderBy('created_at', 'desc')->select('id', 'main_cat_id', lang() . '_name as name', 'thumb_image as image', 'price_meta')->take($take)->get();

        foreach ($products as $product) {
            $product['is_favorite'] = user() ? Favorite::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
            $product['is_cart'] = user() ? Cart::where('user_id', user()->id)->where('product_id', $product->id)->exists() : false;
        }

        return $products;
    }


    public static function getBrands()
    {
        return Brand::whereIn('country_id', [0, country()->id])->where('status', 'active')->inRandomOrder()->take(10)->pluck('image');
    }


    public static function getSocials()
    {
        return Social::where('status', 'active')->select('url', 'image')->get();
    }


    public function about_us()
    {
        $text = About::select(lang() . '_text as text')->first();
        return view('web.about_us', compact('text'));
    }


    public function terms()
    {
        $text = Term::select(lang() . '_text as text')->first();
        return view('web.terms', compact('text'));
    }


    public function news_subscribe(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return back()->with('error', 'email_invalid');
        }

        Newsletter::updateOrCreate(['email' => $request->email]);

        return back()->with('success', 'subscribed_stay_tuned');
    }


    public function contact_view()
    {
        return view('web.contact_us');
    }


    public function contact(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'text' => 'required',
            ],
            [
                'name.required' => 'field_required',
                'email.required' => 'field_required',
                'email.email' => 'email_email',
                'phone.required' => 'field_required',
                'text.required' => 'field_required',
            ]
        );

        Contact::create
        (
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'text' => $request->text,
            ]
        );

        return redirect('/')->with('success', 'sent_we_contact');
    }


    public function search_request_view()
    {
        return view('web.search_request');
    }


    public function search_request(Request $request)
    {
        $this->validate($request,
            [
                'type' => 'required|in:product,seller',
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
                'attachments' => 'sometimes|array',
                'attachments.*' => 'file',
                'text' => 'required'
            ],
            [
                'type.required' => 'field_required',
                'type.in' => 'field_invalid',
                'name.required' => 'field_required',
                'email.required' => 'field_required',
                'email.email' => 'email_email',
                'phone.required' => 'field_required',
                'address.required' => 'field_required',
                'attachments.*.file' => 'field_required',
                'text.required' => 'field_required',
            ]
        );

        $search = new SearchRequest();
        $search->type = $request->type;
        $search->name = $request->name;
        $search->email = $request->email;
        $search->phone = $request->phone;
        $search->text = $request->text;
        $search->address = $request->address;
        if ($request->attachments) {
            $arr = [];
            foreach ($request->attachments as $attachment) {
                $info = unique_file_folder($attachment->getClientOriginalExtension());
                $attachment->move(public_path('/uploads/search_requests/' . $info['month']), $info['image']);

                $arr[] = $info['name'];
            }

            $search->attachments = implode(',', $arr);
        }
        $search->save();

        return redirect('/')->with('success', 'sent_we_contact');
    }
}
