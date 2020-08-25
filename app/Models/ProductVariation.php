<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable =
        [
            'product_id','options','price','sale_price','sale','sale_till','count'
        ];


    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }


    public function getApiSaleTillAttribute($value)
    {
        return $value ? strtotime($value) : 0;
    }


    public static function getVariations($product_id)
    {
        $parent_ids = [];
        $ids = [];

        $variations_options = ProductVariation::where('product_id',$product_id)->pluck('options');

        foreach($variations_options as $options)
        {
            foreach($options as $option)
            {
                $parent_ids[] = VariationOption::where('id',$option)->select('parent_id')->first()->parent_id;
                $ids[] = $option;
            }
        }

        $variations = Variation::whereIn('id',$parent_ids)->select('id',lang().'_name as name')->get();
        foreach($variations as $variation)
        {
            $variation['options'] = VariationOption::where('parent_id',$variation->id)->whereIn('id',$ids)->select('id',lang().'_name as name')->get();
            unset($variation->id);
        }

        return $variations;
    }


    public static function getVariationsOptionsApi($product_id)
    {

        $variations = ProductVariation::where('product_id',$product_id)->select('id','options','price','sale_price','sale','sale_till as api_sale_till','count')->get();

        foreach($variations as $variation)
        {
            $arr = [];
            foreach($variation->options as $option_id) $arr[] = VariationOption::where('id',$option_id)->select(lang().'_name as name')->first()->name;
            $variation['options_str'] = implode(' - ',$arr);
        }

        return $variations;
    }


    public static function getVariationsWeb($product_id)
    {
        $parent_ids = [];
        $ids = [];

        $variations_options = ProductVariation::where('product_id',$product_id)->pluck('options');

        foreach($variations_options as $options)
        {
            foreach($options as $option)
            {
                $parent_ids[] = VariationOption::where('id',$option)->select('parent_id')->first()->parent_id;
                $ids[] = $option;
            }
        }

        $variations = Variation::whereIn('id',$parent_ids)->select('id',lang().'_name as name')->get();
        foreach($variations as $variation) $variation['options'] = VariationOption::where('parent_id',$variation->id)->whereIn('id',$ids)->select(lang().'_name as name')->pluck('name');

        return $variations;
    }


    public static function getVariationsOptionsWeb($product_id)
    {

        $variations = ProductVariation::where('product_id',$product_id)->select('id','options','price','sale_price','sale','sale_till','count')->get();
        return $variations;
    }


    public static function getVariationName($id)
    {
        return Variation::where('id',$id)->select(lang().'_name as name')->first()->name;
    }


    public static function getVariationOptions($id)
    {
        return VariationOption::where('parent_id',$id)->select('id',lang().'_name as name')->get();
    }
}
