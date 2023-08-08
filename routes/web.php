<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLocationController;
use App\Http\Services\Gis\GisService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return redirect('/gis');
});

Route::post('store-location/{phone}', [UserLocationController::class, 'store'])->name('store.location');
Route::post('redirect-user', [UserLocationController::class, 'redirectUser'])->name('redirect.location');
Route::get('map-link', [UserLocationController::class, 'mapLink'])->name('map-link');
Route::post('gen-map-link', [UserLocationController::class, 'mapLink'])->name('gen-map-link');
Route::get('show-map/{phone}', [UserLocationController::class, 'showMap'])->name('show-map');

Auth::routes();

Route::get('/gis', [App\Http\Controllers\Gis\GisBoundaryController::class, 'index'])->name('gis.index');
Route::get('/gis-key-mappings', [App\Http\Controllers\Gis\GisBoundaryController::class, 'gisKeyMappings'])->name('gis.key-mappings');
Route::post('/gis-key-mappings', [App\Http\Controllers\Gis\GisBoundaryController::class, 'storeKeyMappings'])->name('gis.create-key-mappings');
Route::post('/delete-key-mappings', [App\Http\Controllers\Gis\GisBoundaryController::class, 'destroyKeyMappings'])->name('gis.destroy-key-mappings');
Route::get('/gis/create', [App\Http\Controllers\Gis\GisBoundaryController::class, 'create'])->name('gis.create');
Route::post('/gis', [App\Http\Controllers\Gis\GisBoundaryController::class, 'store'])->name('gis.store');
Route::post('/gis/delete', [App\Http\Controllers\Gis\GisBoundaryController::class, 'destroy'])->name('gis.destroy');
Route::post('/gis/dropdown-values', [App\Http\Controllers\Gis\GisBoundaryController::class, 'dropdownValues'])->name('gis.dropdown-values');
Route::post('get-gcs-coordinates', [UserLocationController::class, 'getGeoData'])->name('get-gcs-coordinates');
