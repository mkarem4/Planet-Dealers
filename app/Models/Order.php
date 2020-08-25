<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
            'deleted','country_id','city_id','status','code','seller_id','buyer_id','address_id','items','items_count','items_fee','tax_fee','total_fee','admin_notes','image'
        ];


    public function country()
    {
        return $this->belongsTo(Country::class,'country_id')->select('id',lang().'_name as name',lang().'_currency as currency');
    }


    public function buyer()
    {
        return $this->belongsTo(User::class,'buyer_id')->select('id','first_name','last_name','image');
    }


    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id')->select('id','first_name','last_name','image','bank_info');
    }


    public function getItemsAttribute($value)
    {
        return json_decode($value);
    }

    public function getTimestampAttribute($value)
    {
        return strtotime($value);
    }


    public function getImageAttribute($value)
    {
        return $value ? asset('/uploads/orders/'.$value) : '';
    }


    public function get_status_color($status)
    {
        if($status == 'pending') $status = 'primary';
        if($status == 'confirmed') $status = 'info';
        if($status == 'processing') $status = 'warning';
        if($status == 'delivered') $status = 'success';
        if($status == 'canceled') $status = 'danger';
        if($status == 'declined') $status = 'danger';

        return $status;
    }
}
