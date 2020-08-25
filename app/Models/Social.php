<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $fillable =
        [
            'status','url','image'
        ];


    public function getImageAttribute($value)
    {
        return asset('/uploads/socials/'.$value);
    }
}
