<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('gen-map-link', [UserLocationController::class, 'mapLink'])->name('gen-map-link');

Route::post('get-location-info', [UserLocationController::class, 'getLocationInfo'])->name('get-location-info');

Route::post('gis-info', [UserLocationController::class, 'gisLocationInfo'])->name('gis-location-info');

Route::post('get-gcs-coordinates', [UserLocationController::class, 'getGeoData'])->name('get-gcs-coordinates-api');
