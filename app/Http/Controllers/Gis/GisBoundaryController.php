<?php

namespace App\Http\Controllers\Gis;

use App\Models\Gis\City;
use App\Models\Gis\State;
use App\Models\Gis\District;
use Illuminate\Http\Request;
use App\Models\Gis\GisBoundary;
use App\Models\Gis\GisCoordinate;
use App\Models\Gis\GisKeyMapping;
use App\Models\Gis\GisBoundaryType;
use App\Http\Controllers\Controller;
use App\Http\Services\Gis\GisService;
use App\Models\Gis\GisBoundarySubType;

class GisBoundaryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $gisBoundaries = GisBoundary::with(['state', 'district', 'city', 'boundaryType', 'boundarySubType'])->get();
        return view('gis.index', ['gisBoundaries' => $gisBoundaries]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $states =  State::orderBy('name')->get();
        $districts = [];
        $cities = [];
        $boundaryTypes =  GisBoundaryType::all();
        $boundarySubTypes =  GisBoundarySubType::all();

        return view('gis.create', [
            'states' => $states,
            'districts' => $districts,
            'cities' => $cities,
            'boundaryTypes' => $boundaryTypes,
            'boundarySubTypes' => $boundarySubTypes
        ]);
    }

    public function store(Request $request)
    {
        $gisBoundary = new GisBoundary();
        $gisBoundary->name = $request->description;
        $gisBoundary->state_id = $request->state_id;
        $gisBoundary->district_id = $request->district_id;
        $gisBoundary->city_id = $request->city_id;
        $gisBoundary->gis_boundary_type_id = $request->gis_boundary_type_id;
        $gisBoundary->gis_boundary_sub_type_id = $request->gis_boundary_sub_type_id;
        $gisBoundary->save();

        app(GisService::class)
            ->processGisFile($request->file('gis_file'), $gisBoundary);

        return redirect()->route('gis.index');
    }

    public function destroy(Request $request)
    {
        GisCoordinate::where('gis_boundary_id', $request->id)->delete();
        GisBoundary::where('id', $request->id)->delete();
        return redirect()->route('gis.index');
    }

    public function gisKeyMappings(Request $request)
    {
        $gisKeysMappings = GisKeyMapping::with('gisBoundarySubType')
            ->orderBy('gis_boundary_sub_type_id')
            ->get();
        $subTypes = GisBoundarySubType::orderBy('slug')->get();

        return view('gis.key-mappings', [
            'gisKeysMappings' => $gisKeysMappings,
            'subTypes' => $subTypes
        ]);
    }

    public function storeKeyMappings(Request $request)
    {
        $gisKeyMapping = GisKeyMapping::where('gis_key', $request->gis_key)->first();

        if (optional($gisKeyMapping)->gis_key) {
            return redirect()->route('gis.key-mappings')->withErrors(['msg' => "'{$request->gis_key}' Gis key already exists"]);
        }

        $gisKeyMapping = new GisKeyMapping();
        $gisKeyMapping->gis_boundary_sub_type_id = $request->gis_boundary_sub_type_id;
        $gisKeyMapping->gis_key = $request->gis_key;
        $gisKeyMapping->save();
        return redirect()->route('gis.key-mappings');
    }

    public function destroyKeyMappings(Request $request)
    {
        GisKeyMapping::where('id', $request->id)->delete();
        return redirect()->route('gis.key-mappings');
    }

    public function dropdownValues(Request $request)
    {
        return response()
            ->json($this->getDropDownValues($request->type, $request->id));
    }

    public function getDropDownValues($type, $id)
    {
        switch ($type) {
            case 'state':
                return District::where('state_id', $id)->orderBy('name')->get();
                break;

            case 'district':
                return City::where('district_id', $id)->orderBy('name')->get();
                break;

            case 'gis_boundary':
                return GisBoundarySubType::where('gis_boundary_type_id', $id)->orderBy('name')->get();
                break;

            default:
                return [];
                break;
        }
    }
}
