<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
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
            'deleted','user_id','city_id','type','text','number','close_to','notes'
        ];


    public function getCityIdAttribute($value)
    {
        return (integer)$value;
    }


    public function city()
    {
        return $this->belongsTo(Country::class,'city_id')->select('id',lang().'_name as name');
    }
}
