<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
            'deleted','status','type','parent_id','ar_name','en_name','image'
        ];



    public function parent()
    {
        return $this->belongsTo(Category::class,'parent_id')->select('id',lang().'_name as name');
    }


    public function subs()
    {
        return $this->hasMany(Category::class,'parent_id')->select('id',lang().'_name as name');
    }


    public function getImageAttribute($value)
    {
        return asset('/uploads/categories/'.$value);
    }


    public function getLNameAttribute()
    {
        $name = lang().'_name';
        return $this->$name;
    }
}
