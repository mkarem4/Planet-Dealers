<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function index()
    {
        $ids = Favorite::where('user_id',user()->id)->pluck('product_id');
        $products = Product::whereIn('id',$ids)->where('status','active')->select('id','main_cat_id',lang().'_name as name','image','price_meta')->paginate(21);

        return view('web.wishlist',get_defined_vars());
    }
    public function handle(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'product_id' => 'required|exists:products,id,deleted,0,status,active'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $check = Favorite::where('user_id',user()->id)->where('product_id',$request->product_id)->first();

        if($check)
        {
            $check->delete();
            return r_json(['type' => false]);
        }
        else
        {
            Favorite::create(['user_id' => user()->id,'product_id' => $request->product_id]);
            return r_json(['type' => true]);
        }
    }
}
