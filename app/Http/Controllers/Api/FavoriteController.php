<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $ids = Favorite::where('user_id',$request->user_id)->pluck('product_id');
        $products = Product::where('status','active')->whereIn('id',$ids)->select('id','type',lang().'_name as name','thumb_image as image','price_meta')->paginate(20);

        return r_json($products);
    }


    public function handle(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'product_id' => 'required|exists:products,id,deleted,0,status,active'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $check = Favorite::where('user_id',$request->user_id)->where('product_id',$request->product_id)->first();

        if($check) $check->delete();
        else Favorite::create(['user_id' => $request->user_id,'product_id' => $request->product_id]);

        return r_json([],204);
    }
}
