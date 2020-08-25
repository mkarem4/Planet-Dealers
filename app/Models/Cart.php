<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable =
        [
            'user_id','seller_id','product_id','product_variation_id','count','price'
        ];


    public function getProductVariationIdAttribute($value)
    {
        return $value ? $value : 0;
    }
}
