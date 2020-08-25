<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable =
        [
            'country_id','status','url','image','expire_at'
        ];


    public function getImageAttribute($value)
    {
        return asset('/uploads/banners/'.$value);
    }
}
