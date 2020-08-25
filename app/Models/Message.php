<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable =
        [
            'seen','sender_id','target_id','text'
        ];


    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id')->select('id','first_name','last_name','lang');
    }


    public function target()
    {
        return $this->belongsTo(User::class,'target_id')->select('id','first_name','last_name','lang');
    }


    public function getTimestampAttribute($value)
    {
        return strtotime($value);
    }


    public function getDateTimeAttribute($value)
    {

        return $value;
    }


    public function scopeRead($q)
    {
        $q->update(['seen' => 1]);
        return $q;
    }
}
