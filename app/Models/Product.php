<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }


    protected $fillable =
        [
            'deleted','country_id','city_id','seller_id','status','type','ar_name','en_name','image','price_meta','rate','views','sold','discount','discount_till'
        ];


    public function country()
    {
        return $this->belongsTo(Country::class,'country_id')->select('id',lang().'_name as name',lang().'_currency as currency');
    }


    public function main_cat()
    {
        return $this->belongsTo(Category::class,'main_cat_id')->select('id','parent_id',lang().'_name as name');
    }


    public function sub_cat()
    {
        return $this->belongsTo(Category::class,'sub_cat_id')->select('id','parent_id',lang().'_name as name');
    }


    public function sec_cat()
    {
        return $this->belongsTo(Category::class,'sec_cat_id')->select('id','parent_id',lang().'_name as name');
    }


    public function getImageAttribute($value)
    {
        return asset('/uploads/products/'.$value);
    }


    public function getThumbImageAttribute($value)
    {
        return asset('/uploads/products/'.$value);
    }


    public function getPriceMetaAttribute($value)
    {
        $value = json_decode($value);
//        dd($value);
        $value->sale_till = $value->sale_till ? $value->sale_till : '';

        return $value;
    }


    public function get_stars($n)
    {
        $stars = '';

        for($x =0; $x < $n; $x++) $stars .= '<i class="fa fa-star"></i>';
        for($i = $x; $i < 5; $i++) $stars .= '<i class="fa fa-star-o"></i>';

        return $stars;
    }


    public static function getRateCount($product_id,$rate)
    {
        return ProductRate::where('product_id',$product_id)->where('rate',$rate)->count();
    }


    public static function getRatePercentage($product_id,$rate)
    {
        $total = Product::where('id',$product_id)->select('rate_count')->first()->rate_count;
        $count = ProductRate::where('product_id',$product_id)->where('rate',$rate)->count();

        if($total == 0)
        {
            $arr['percent_1'] = 0;
            $arr['percent_2'] = 0;
        }
        else
        {
            $arr['percent_1'] = $count / $total * 100;
            $arr['percent_2'] = 100 - $arr['percent_1'];
        }

        return $arr;
    }


    public function getMeta($id,$key)
    {
        $meta = ProductMeta::where('product_id',$id)->where('key',$key)->first();
        return $meta ? $meta->value : '';
    }
}
