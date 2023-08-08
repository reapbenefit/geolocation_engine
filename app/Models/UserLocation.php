<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'user_id',
        'meta_data',
    ];



    public function getLocationMeta()
    {
        $str = $this->meta_data;
        $str =  json_decode($str, true);
        $metaData = $str;
        if (!is_array($str)) {
            ## Will fix this later
            $metaData = json_decode($str, true);
        }

        $addressComponents =  [];

        foreach ($metaData['address_components'] ?: [] as $component) {
            $addressComponents[$component['types'][0]] = $component['long_name'];
        }

        $addressComponents['lat'] = $this->latitude;
        $addressComponents['lng'] = $this->longitude;
        $addressComponents['formatted_address'] = $metaData['formatted_address'] ?? '';

        return [
            'address_components' => $addressComponents,
            'formatted_address' => $metaData['formatted_address'],
            'place_id' => $metaData['place_id'],
        ];
    }
}
