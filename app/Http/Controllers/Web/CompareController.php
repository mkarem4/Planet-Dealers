<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMeta;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CompareController extends Controller
{
    public function index()
    {
        $ids = Session::get('compare_ids') ? Session::get('compare_ids') : [];
        $products = Product::where('status','active')->whereIn('id',$ids)->select('id','type',lang().'_name as name','thumb_image as image','price_meta','rate')->get();

        foreach($products as $product)
        {
            $product['desc'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_desc')->select('value')->first()->value;
            $product['custom'] = ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->exists() ? ProductMeta::where('product_id',$product->id)->where('key',lang().'_custom')->select('value')->first()->value : '';

            if($product->type == 'variable')
            {
                $product['variations'] = ProductVariation::getVariationsWeb($product->id);
                $product['variations_data'] = ProductVariation::getVariationsOptionsApi($product->id);
            }
        }

        return view('web.compare',compact('products'));
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

        $ids = Session::get('compare_ids');

        if($ids)
        {
            if(in_array($request->product_id,$ids))
            {
                unset($ids[array_search($request->product_id,$ids)]);
                Session::put('compare_ids',$ids);

                return r_json(['type' => false]);
            }
            else
            {
                if(count($ids) >= 3) return r_json(['type' => 'full']);

                Session::push('compare_ids',$request->product_id);
                return r_json(['type' => true]);
            }
        }
        else
        {
            Session::push('compare_ids',$request->product_id);
            return r_json(['type' => true]);
        }

    }
}
