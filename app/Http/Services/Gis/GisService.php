<?php

namespace App\Http\Services\Gis;

use App\Models\Gis\GisBoundarySubType;
use App\Models\Gis\GisCoordinate;
use App\Models\Gis\GisKeyMapping;
use App\Models\User;
use App\Models\UserLocation;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\Polygon;
use MStaack\LaravelPostgis\Geometries\LineString;
use DOMDocument;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessLocation;

class GisService
{
    protected $gisKeyMapping = [];

    public function processGisFile($file, $gisBoundary)
    {
        $ext =  $file->getClientOriginalExtension();

        if ($ext == 'kml') {
            $this->processKml($file, $gisBoundary);
            return true;
        }

        abort(500, 'Unsupported file type');
    }

    public function getGisBoundary($lat, $lang)
    {
        $gisKeys = GisBoundarySubType::all()->pluck('slug');

        $gisCoordinates =  GisCoordinate::with(['gisBoundary', 'gisBoundary.state', 'gisBoundary.district', 'gisBoundary.city', 'gisBoundary.boundaryType', 'gisBoundary.boundarySubType'])
            ->whereRaw('ST_Intersects(ST_SetSRID(ST_POINT(?, ?), 4326)::geography, "polygon")', [$lang, $lat])
            ->orderBy('id')
            ->get(['id', 'gis_boundary_id', 'properties']);

        $results = [];
        foreach ($gisCoordinates as $gisCoordinate) {
            $gisBoundary = $gisCoordinate->gisBoundary;

            if (!$gisBoundary) {
                continue;
            }

            if ($gisBoundary->state) {
                $results['u_state'] =  $gisBoundary->state->name;
            }

            if ($gisBoundary->district) {
                $results['u_district'] =  $gisBoundary->district->name;
            }

            if ($gisBoundary->city) {
                $results['u_city'] =  $gisBoundary->city->name;
            }

            foreach ($gisKeys as $gisKey) {
                $value = $gisCoordinate->properties[$gisKey] ?? null;
                if ($value) {
                    $results[$gisKey] =  $value;
                }
            }
        }
        return $results;
    }

    private function processKml($file, $gisBoundary)
    {
        $xml = simplexml_load_file($file);

        $placeMarks = $this->getKmlPlacesMarks($xml);

        foreach ($placeMarks as $placeMark) {
            $name = $placeMark->name;
            $properties = $this->getKmlProperties($placeMark) ?: [];
            $this->updateKmlKeyMapping(array_keys($properties));
            $properties = $this->formatKmlProperties($properties);
            $coordinates = $this->getKmlCoordinates($placeMark);
            $coordinates = explode(' ', $coordinates);
            $points = [];
            foreach ($coordinates as $coordinate) {
                if (!$coordinate) {
                    continue;
                }

                $coordinate = explode(',', $coordinate);
                $lat = $coordinate[1] ?? null;
                $lng = $coordinate[0] ?? null;

                if (!$lat || !$lng) {
                    continue;
                }

                $points[] = new Point($lat, $lng);
            }

            $lineString = new LineString($points);

            $polygon = new Polygon([$lineString]);

            if (!$polygon) {
                continue;
            }

            GisCoordinate::create([
                'name' => $name,
                'polygon' => $polygon,
                'gis_boundary_id' => $gisBoundary->id,
                'properties' => $properties
            ]);
        }
    }

    private function formatKmlProperties($props)
    {
        foreach ($props as $key => $value) {
            $gisKey = $this->gisKeyMapping[$key] ?? null;
            if ($gisKey) {
                $props[$gisKey] = $value;
            }
        }
        return $props;
    }

    private function updateKmlKeyMapping($keys)
    {
        if ($this->gisKeyMapping) {
            return $this->gisKeyMapping;
        }

        $gisMappings = GisKeyMapping::with('gisBoundarySubType')->whereIn('gis_key', $keys)->get();

        foreach ($gisMappings as $gisMapping) {
            $this->gisKeyMapping[$gisMapping->gis_key] = $gisMapping->gisBoundarySubType->slug;
        }

        return $this->gisKeyMapping;
    }


    private function getKmlPlacesMarks($xml)
    {
        if (isset($xml->Document->Placemark)) {
            return $xml->Document->Placemark;
        }

        if (isset($xml->Document->Folder->Placemark)) {
            return $xml->Document->Folder->Placemark;
        }

        abort(500, 'Could not found place marks');
    }

    private function getKmlCoordinates($placeMark)
    {
        if (isset($placeMark->Polygon)) {
            return $placeMark->Polygon->outerBoundaryIs->LinearRing->coordinates;
        }

        if (isset($placeMark->MultiGeometry)) {
            return $placeMark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;
        }

        //  We will add more conditions

        abort(500, "Could not fetch coordinates");
    }

    private function getKmlProperties($placeMark)
    {
        if (isset($placeMark->ExtendedData)) {
            return $this->convertKmlExtendedDataToArray($placeMark->ExtendedData);
        }

        if (isset($placeMark->description) && (string) $placeMark->description != '') {
            return $this->convertKmlDescriptionToArray((string) $placeMark->description);
        }

        return [];
    }

    private function convertKmlExtendedDataToArray($extendedDataObj)
    {
        $type = null;

        if (isset($extendedDataObj->Data)) {
            $type = 'Data';
            $extendedData = $extendedDataObj->Data;
        }

        if (isset($extendedDataObj->SchemaData->SimpleData)) {
            $type = 'SimpleData';
            $extendedData = $extendedDataObj->SchemaData->SimpleData;
        }

        $properties = [];
        foreach ($extendedData as $extendedDataItem) {
            $name = (string) $extendedDataItem->attributes()->name;
            $value = (string) $extendedDataItem->value;

            if ($type == 'SimpleData') {
                $value =  (string)$extendedDataItem;
            }
            $properties[$name] = $value;
        }

        return $properties;
    }

    private function convertKmlDescriptionToArray($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $length = $dom->getElementsByTagName('table')->length;
        $table =  $dom->getElementsByTagName('table')->item($length - 1);
        $properties = [];
        foreach ($table->childNodes as $node) {
            if ($node->nodeName == 'tr') {
                $tds = $node->getElementsByTagName('td');
                $name = $tds->item(0)->nodeValue;
                $value = $tds->item(1)->nodeValue;
                $properties[$name] = $value;
            }
        }

        return $properties;
    }

    public function getGeoData($lat, $lang, $phone)
    {
        $response = Http::post('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lang.'&key='.config('services.google-map.api_key'));
        $response = json_decode($response->body());
        $user = User::getUserByPhone($phone);
        $userLocation = UserLocation::create([
            'name' => "google_api_response",
            "user_id" => $user->id,
            "address" => $response->results[0]->formatted_address,
            "latitude" => $lat,
            "longitude" => $lang,
            "meta_data" =>  json_encode($response->results[0]),
        ]);

        ProcessLocation::dispatch($user->id, $userLocation->id, null);
        return $userLocation;
    }
}
