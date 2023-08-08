<?php

namespace App\Models\Gis;


use Illuminate\Database\Eloquent\Model;

class GisBoundary extends Model
{
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function boundaryType()
    {
        return $this->belongsTo(GisBoundaryType::class, 'gis_boundary_type_id');
    }

    public function boundarySubType()
    {
        return $this->belongsTo(GisBoundarySubType::class, 'gis_boundary_sub_type_id');
    }
}
