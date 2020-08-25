<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable =
        [
            'country_id','status','url','image','image_mobile','expire_at'
        ];

    public function getImageAttribute($value)
    {
        return asset('/uploads/slides/'.$value);
    }


    public function getImageMobileAttribute($value)
    {
        return asset('/uploads/slides/'.$value);
    }
}
