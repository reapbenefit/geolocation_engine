<?php

namespace App\Models\Gis;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = [];

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }
}
