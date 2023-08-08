<?php

namespace App\Models\Gis;


use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class GisCoordinate extends Model
{
    use PostgisTrait;

    protected $guarded = [];

    protected $table = 'gis_coordinates';

    protected $casts = [
        'properties' => 'array',
    ];

    protected $postgisFields = [
        'polygon'
    ];

    protected $postgisTypes = [
        'polygon' => [
            'geomtype' => 'geography',
            'srid' => 4326
        ]
    ];

    public function gisBoundary()
    {
        return $this->belongsTo(GisBoundary::class);
    }
}
