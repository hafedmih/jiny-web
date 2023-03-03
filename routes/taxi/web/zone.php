<?php 

use App\Http\Controllers\Taxi\Web\ZoneManagement\ZoneController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('zone', [ZoneController::class,'index'])->name('zone');
    Route::get('zone/add', [ZoneController::class,'addzone'])->name('addzone');
    Route::post('zone/store', [ZoneController::class,'saveZone'])->name('saveZone');
    Route::post('zone/update', [ZoneController::class,'updateZone'])->name('updateZone');
    Route::get('zone/delete/{id}', [ZoneController::class,'deleteZone'])->name('deleteZone');
    Route::get('zone/active/{id}', [ZoneController::class,'activeZone'])->name('activeZone');
    Route::get('zone/edit/{id}', [ZoneController::class,'editZone'])->name('editZone');
    Route::get('zone/view/{id}', [ZoneController::class,'viewMapZone'])->name('viewMapZone');
    Route::get('zone/details/{id}', [ZoneController::class,'getZoneDetails'])->name('getZoneDetails');
    Route::get('zone/get/type/prices', [ZoneController::class,'getTypePrices'])->name('getTypePrices');
    Route::get('zone/surge/price/{slug}', [ZoneController::class,'getZoneSrugePrice'])->name('getZoneSrugePrice');
    Route::post('zone/surge/price/save', [ZoneController::class,'getZoneSrugePriceSave'])->name('getZoneSrugePriceSave');
    Route::get('zone/view', [ZoneController::class,'mapView'])->name('mapView');

    Route::get('fare/amounts', [ZoneController::class,'viewFareAmount'])->name('viewFareAmount');
});

Route::group(['prefix' => 'service-location', 'as'=>'service-location.', 'middleware' => ['auth']], function () {
    // Route::get('/', [ServiceLocationController::class,'index'])->name('index');
    // Route::get('/add', [ServiceLocationController::class,'add'])->name('add');
    // Route::post('/store', [ServiceLocationController::class,'store'])->name('store');
    // Route::get('/edit/{serviceLocation}', [ServiceLocationController::class,'edit'])->name('edit');
    // Route::post('/update/{serviceLocation}', [ServiceLocationController::class,'update'])->name('update');
    // Route::get('/status/{serviceLocation}', [ServiceLocationController::class,'status'])->name('status');
    // Route::get('/delete/{serviceLocation}', [ServiceLocationController::class,'delete'])->name('delete');
    Route::post('/getcoords', [ZoneController::class,'getCoordsByKeyword'])->name('suggestion');
    // Route::get('/getcity', [ServiceLocationController::class,'getAllCity'])->name('getcity');
});
