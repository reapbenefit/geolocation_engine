<?php

namespace App\Models\Gis;


use Illuminate\Database\Eloquent\Model;

class GisKeyMapping extends Model
{
    protected $guarded = [];

    public function gisBoundarySubType()
    {
        return $this->belongsTo(GisBoundarySubType::class);
    }
}
