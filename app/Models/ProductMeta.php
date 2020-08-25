<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductMeta extends Model
{
    protected $fillable =
        [
            'product_id','key','value'
        ];


    public function getImageAttribute($value)
    {
        return asset('/uploads/products/'.$value);
    }
}
