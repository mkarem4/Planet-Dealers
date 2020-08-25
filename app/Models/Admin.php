<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
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
            'deleted','country_id','status','name','email','phone','password','image','lang'
        ];


    public function getImageAttribute($value)
    {
        return asset('/uploads/users/'.$value);
    }
}
