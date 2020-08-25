<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Slide;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CroneController extends Controller
{
    public function users_featured()
    {
        $rows = User::where('featured_till','<',Carbon::today()->toDateString())->select('id','featured','featured_till')->get();

        foreach($rows as $row)
        {
            $row->featured = 0;
            $row->featured_till = NULL;
            $row->save();
        }

        return 'success';
    }


    public function users_packs()
    {
        $rows = User::where('expire_at','<',Carbon::today()->toDateString())->select('id','pack_id','expire_at')->get();

        foreach($rows as $row)
        {
            $row->pack_id = 0;
            $row->expire_at = NULL;
            $row->save();
        }

        return 'success';
    }


    public function slides()
    {
        $rows = Slide::where('expire_at','<',Carbon::today()->toDateString())->select('id','status')->get();

        foreach($rows as $row)
        {
            $row->status = 'expired';
            $row->save();
        }

        return 'success';
    }


    public function products_featured()
    {
        $rows = Product::where('featured_till','<',Carbon::today()->toDateString())->select('id','featured','featured_till')->get();

        foreach($rows as $row)
        {
            $row->featured = 0;
            $row->featured_till = NULL;
            $row->save();
        }

        return 'success';
    }


    public function products_discounts()
    {
        $rows = Product::where('discount_till','<',Carbon::today()->toDateString())->select('id','discount','discount_till')->get();

        foreach($rows as $row)
        {
            $row->discount = 0;
            $row->discount_till = NULL;
            $row->save();
        }

        return 'success';
    }


    public function products_discounts_variations()
    {
        $rows = ProductVariation::where('sale_till','<',Carbon::today()->toDateString())->select('id','product_id','sale','sale_till')->get();

        foreach($rows as $row)
        {
            $row->sale = 0;
            $row->sale_till = NULL;
            $row->save();

            $check = ProductVariation::where('product_id',$row->product_id)->select('id')->first();
            if($check->id == $row->id) Product::where('id',$row->product_id)->update(['discount' => 0,'discount_till' => NULL]);
        }

        return 'success';
    }
}
