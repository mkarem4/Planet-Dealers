<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    protected $fillable =
        [
            'status','deleted','country_id','pack_id','bank_id','user_id','admin_id','admin_notes','user_name','user_notes','account_no','image'
        ];


    public function bank()
    {
        return $this->belongsTo(Bank::class,'bank_id')->select('id',lang().'_name as name');
    }


    public function pack()
    {
        return $this->belongsTo(Pack::class,'pack_id')->select('id',lang().'_name as name','price');
    }

    public function getImageAttribute($value)
    {
        return asset('/uploads/bank_transfers/'.$value);
    }


    public function setUserNotesAttribute($value)
    {
        $this->attributes['user_notes'] = $value ? $value : '';
    }
}
