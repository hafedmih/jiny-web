<?php 

use App\Http\Controllers\Taxi\Web\OutstationMaster\OutstationpackageController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('outstationpackage', [OutstationpackageController::class,'outstationPackage'])->name('outstation-package');
     Route::post('outstationpackagesave', [OutstationpackageController::class,'outstationPackageSave'])->name('outstationpackagesave');
    Route::post('outstationpackageupdate', [OutstationpackageController::class,'outstationPackageUpdate'])->name('outstationpackageupdate');
    Route::get('outstationpackageedit/{id}', [OutstationpackageController::class,'outstationPackageedit'])->name('outstationpackageedit');
    Route::get('outstationpackagedelete/{id}', [OutstationpackageController::class,'outstationPackageDelete'])->name('outstationpackagedelete');
    Route::get('outstationpackagestatus-active/{id}', [OutstationpackageController::class,'outstationPackageChangeStatus'])->name('outstationpackageactive');

    
   
});