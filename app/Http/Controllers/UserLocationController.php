<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Jobs\ProcessLocation;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\Gis\GisService;
use App\Http\Services\Glific\GlificService;

class UserLocationController extends Controller
{
    public function store(Request $request, $phone)
    {
        $flowID = $request->flow_id;
        $contactId = $request->contact_id;
        $loadingMessageFlowId = $request->loading_message_flow_id;

        // This is just for references to the user.
        // Later we can extends it to glific contacts etc.

        $user =  User::firstOrCreate(
            [
                'phone' => $phone
            ],
            [
                'password' => Hash::make(Str::random(20)),
                'name' => $phone,
                'email' => $phone . '@example.com',
                'contact_id' => $contactId
            ]
        );

        if (!$user->contact_id && $contactId) {
            $user->contact_id = $contactId;
            $user->save();
        }

        $userLocation = UserLocation::create([
            'user_id' => $user->id,
            'name' => 'user_location',
            'address' => $request->address_address,
            'latitude' => $request->address_latitude,
            'longitude' => $request->address_longitude,
            'meta_data' => json_encode($request->address_meta_data)
        ]);

        ProcessLocation::dispatchAfterResponse($user->id, $userLocation->id, $flowID);

        if ($loadingMessageFlowId) {
            $service  =  app(GlificService::class);
            $service->startFlowForContact($user->contact_id, $loadingMessageFlowId);
        }

        $redirectUrl = 'https://api.whatsapp.com/send?phone=' . config('services.glific.business_phone');

        return redirect()->to($redirectUrl);
    }

    public function redirectUser(Request $request)
    {
        return redirect()->to($request->redirect_url);
    }

    public function mapLink(Request $request)
    {
        $phone =  $request->phone;
        $requestData = $request->request_data ?: [];
        return [
            'link' => route('show-map', array_merge($requestData, ['phone' => $phone])),
            'short_link' => 'show-map/' . $phone . '?' . http_build_query($requestData),
        ];
    }

    public function showMap(Request $request, $phone)
    {
        return view('map', [
            'phone' => $phone,
        ]);
    }

    public function gisLocationInfo(Request $request)
    {
        return app(GisService::class)->getGisBoundary($request->lat, $request->lang);
    }

    public function getLocationInfo(Request $request)
    {
        $phone =  $request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'user not found',
            ];
        }

        $userLocation = UserLocation::where('user_id', $user->id)->latest()->first();

        if (!$userLocation) {
            return [
                'status' => 'error',
                'message' => 'user location not found',
            ];
        }

        $data =  $userLocation->getLocationMeta()['address_components'];
        return array_merge($data, [
            'status' => 'success',
            'text' => $this->getLocationResponseText($userLocation),
            'json' => json_encode($data),
        ]);
    }

    private function getLocationResponseText($userLocation)
    {
        $userLocationMetaData = $userLocation->getLocationMeta()['address_components'];
        $str = "Your location has been saved. Thank you!\n";

        foreach ($userLocationMetaData as $key => $value) {
            $str .= "*{$key}* : {$value}\n";
        }

        return $str;
    }

    public function getGeoData(Request $request)
    {
        $data = app(GisService::class)->getGeoData($request->lat, $request->lang, $request->phone);
        return [
            'status' => 'success',
            'data' => $data,
        ];
    }
}
