<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $fillable =
        [
            'email','code','expire_at'
        ];

    public $timestamps = false;
}
