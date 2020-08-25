<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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
            'deleted','country_id','city_id','jwt','status','type','first_name','last_name','company_name','bank_info','commercial_record','email','phone','whatsapp','password','image','lang','pack_id','expire_at','featured','featured_till'
        ];


    protected $hidden =
        [
            'deleted','status','password','created_at','updated_at'
        ];


    public function country()
    {
        return $this->belongsTo(Country::class,'country_id')->select('id',lang().'_name as name',lang().'_currency as currency','tax_percentage');
    }


    public function city()
    {
        return $this->belongsTo(Country::class,'city_id')->select('id',lang().'_name as name');
    }


    public function mini_nots()
    {
        return $this->hasMany(Notification::class,'user_id')->select('id','type','action_id',lang().'_text as text','created_at')->latest()->take(6);
    }


    public function pack()
    {
        return $this->belongsTo(Pack::class,'pack_id')->select('id',lang().'_name as name','price','month_count');
    }


    public function getCartMini()
    {
        $cart['count'] =  round(Cart::where('user_id',user()->id)->count(),2);
        $cart['sum'] =  round(Cart::where('user_id',user()->id)->sum('price'),2);
        $cart['products'] = Cart::where('user_id',user()->id)->select('id','product_id','product_variation_id','count','price')->take(3)->get();
        foreach($cart['products'] as $product)
        {
            $product['product'] = Product::where('id',$product->product_id)->select(lang().'_name as name','thumb_image as image')->first();
        }

        return $cart;
    }


    public function getCountryIdAttribute($value)
    {
        return (integer)$value;
    }


    public function getCityIdAttribute($value)
    {
        return (integer)$value;
    }


    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }


    public function getPackIdAttribute($value)
    {
        return $value ? $value : 0;
    }


    public function getCompanyNameAttribute($value)
    {
        return $value ? $value : '';
    }


    public function getExpireAtAttribute($value)
    {
        return $value ? $value : '';
    }


    public function getFeaturedTillAttribute($value)
    {
        return $value ? $value : '';
    }


    public function getCommercialRecordAttribute($value)
    {
        return $value ? asset('/uploads/users/commercial_records/'.$value) : '';
    }


    public function getImageAttribute($value)
    {
        return asset('/uploads/users/'.$value);
    }


    public function getBankInfoAttribute($value)
    {
        return $value ? $value : '';
    }


    public function getAddressesAttribute()
    {
        $addresses = Address::where('user_id',$this->id)->select('id','city_id','text','number','close_to','notes')->get();
        foreach($addresses as $address)
        {
            $address['text'] = 'm_p'.$address->text;
            $address['number'] = 'm_p'.$address->number;
            $address['close_to'] = 'm_p'.$address->close_to;
            $address['notes'] = 'm_p'.$address->notes;
            $address['city'] = Country::where('id',$address->city_id)->select(lang().'_name as name')->first()->name;
        }

        return $addresses;
    }


    public static function getLatestMessage($user_1,$user_2)
    {
        return Message::where(function($q) use($user_1,$user_2)
        {
            $q->where('sender_id',$user_1)->where('target_id',$user_2);
            $q->orWhere('sender_id',$user_2)->where('target_id',$user_1);
        })->latest()->select('seen','text','created_at')->first();
    }


    public static function getLatestMessageApi($user_1,$user_2)
    {
        return Message::where(function($q) use($user_1,$user_2)
        {
            $q->where('sender_id',$user_1)->where('target_id',$user_2);
            $q->orWhere('sender_id',$user_2)->where('target_id',$user_1);
        })->latest()->select('seen','text','created_at as timestamp')->first();
    }


    public static function getTaxPercentage($user_id,$cart_total)
    {
        $country_id = User::where('id',$user_id)->select('country_id')->first()->country_id;
        $tax_percentage = Country::where('id',$country_id)->select('tax_percentage')->first()->tax_percentage;

        return ($cart_total * $tax_percentage) / 100;
    }


    public function get_unread()
    {
        return Message::where('target_id',user()->id)->where('seen',0)->count();
    }
}
