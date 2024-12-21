<?php 

use App\Http\Controllers\Taxi\Web\Dispatcher\CreateDispatcherRequestController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/dispatcherRequest', [CreateDispatcherRequestController::class,'dispatcher'])->name('dispatcherRequest');

    Route::post('get-vehicle-types', [CreateDispatcherRequestController::class,'getVehicleTypes'])->name('getVehicleTypesList');
    Route::post('get-vehicle-drivers', [CreateDispatcherRequestController::class,'getVehicleDrivers'])->name('getVehicleDriversList');
    Route::post('create-dispatcher-trip-set-amount', [CreateDispatcherRequestController::class,'createDispatchRequestSetAmount'])->name('createDispatchRequestSetAmount');
   
});