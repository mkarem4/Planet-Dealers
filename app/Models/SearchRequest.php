<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchRequest extends Model
{
    protected $fillable =
        [
            'closed'
        ];


    public function getAttachmentsAttribute($value)
    {
        return explode(',',$value);
    }
}
