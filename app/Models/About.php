<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable =
        [
            'ar_text','en_text','android_link','ios_link'
        ];
}
