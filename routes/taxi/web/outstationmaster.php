<?php 

use App\Http\Controllers\Taxi\Web\OutstationMaster\OutstationMasterController;
use App\Http\Controllers\Taxi\Web\OutstationMaster\OutstationListController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('outstationlist', [OutstationMasterController::class,'outstationMaster'])->name('out-station');
    Route::post('outstationmastersave', [OutstationMasterController::class,'outstationSave'])->name('outstationsave');
    Route::post('outstationmasterupdate', [OutstationMasterController::class,'outstationUpdate'])->name('outstationupdate');
    Route::get('outstationmasteredit/{id}', [OutstationMasterController::class,'outstationEdit'])->name('outstationedit');
    Route::get('outstationmasterdelete/{id}', [OutstationMasterController::class,'outstationDelete'])->name('outstationdelete');
    Route::get('outstationstatus-active/{id}', [OutstationMasterController::class,'outstationChangeStatus'])->name('outstationactive');

    
    Route::get('outstation/set-price', [OutstationMasterController::class,'outstationSetPrice'])->name('outstationSetPrice');
    Route::post('outstation/set-price/save', [OutstationMasterController::class,'outstationSetPriceSave'])->name('outstationSetPriceSave');
    // Route::post('outstationmasterupdate', [outstationmasterController::class,'outstationmasterUpdate'])->name('outstationmasterupdate');
    Route::get('outstation/set-price/edit/{id}', [OutstationMasterController::class,'outstationSetPriceedit'])->name('outstationSetPriceEdit');
    // Route::get('outstationmasterdelete/{slug}', [outstationmasterController::class,'outstationmasterDelete'])->name('outstationmasterdelete');
});

Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('outstationTriplist', [OutstationListController::class,'outstationList'])->name('outstationTriplist');
    
});