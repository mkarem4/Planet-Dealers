<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable =
        [
            'country_id','status','image','expire_at'
        ];


    public function getImageAttribute($value)
    {
        return asset('/uploads/brands/'.$value);
    }
}
