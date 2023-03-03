<?php 

use App\Http\Controllers\Taxi\Web\Package\PackageMasterController;
use App\Http\Controllers\Taxi\Web\Package\RentallistController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('packagelist', [PackageMasterController::class,'packagelist'])->name('packagelist');
    Route::get('packagecreate', [PackageMasterController::class,'packagecreate'])->name('packagecreate');
    Route::post('packagecreate', [PackageMasterController::class,'packageSave'])->name('packagesave');
    Route::get('package-edit/{slug}', [PackageMasterController::class,'packageEdit'])->name('packageedit');
    Route::post('packageupdate', [PackageMasterController::class,'packageUpdate'])->name('packageupdate');
    Route::get('packagedelete/{slug}', [PackageMasterController::class,'packageDelete'])->name('packagedelete');
    Route::get('packagestatus-active/{slug}', [PackageMasterController::class,'packageStatusChange'])->name('packageactive');

});

Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('rentallist', [RentallistController::class,'rentalList'])->name('rental');
    
});