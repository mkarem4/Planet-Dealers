<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable =
        [
            'token','user_id','lang'
        ];
}
