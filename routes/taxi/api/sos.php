<?php 

use App\Http\Controllers\Taxi\API\SosController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sos'],function(){
    Route::get('/', [SosController::class,'sosList']);
    Route::post('store',[SosController::class,'store'])->name('api.sos.store');
    Route::post('update/{sos}',[SosController::class,'update'])->name('api.sos.update');
    Route::delete('delete/{slug}',[SosController::class,'delete']);

    // Route::get('driver/tap',[SosController::class,'driverTapSos']);
});