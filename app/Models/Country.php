<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
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
            'deleted','type','status','parent_id','ar_name','en_name','ar_currency','en_currency','tax_percentage','code'
        ];


    public function parent()
    {
        return $this->belongsTo(Country::class,'parent_id')->where('status','active')->select('id',lang().'_name as name',lang().'_currency as currency','tax_percentage','code');
    }


    public function children()
    {
        return $this->hasMany(Country::class,'parent_id')->where('status','active')->select('id',lang().'_name as name');
    }


    public function getLNameAttribute()
    {
        $name = lang().'_name';
        return $this->$name;
    }
}
