<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $table = 'ltm_translations';

    protected $fillable =
        [
            'locale','group','key'
        ];
}
