<?php 

use App\Http\Controllers\Taxi\Web\Vehicle\VehicleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('vehicle', [VehicleController::class,'vehicle'])->name('vehicle');
    Route::get('vehicle-add', [VehicleController::class,'index'])->name('vehicleadd');
    Route::post('vehicle-add', [VehicleController::class,'vehicleStore'])->name('vehicleSave');
    Route::get('/editvehicle/{id}', [VehicleController::class,'vehicleEdit'])->name('vehicleEdit');
    Route::post('/vehicleUpdate', [VehicleController::class,'vehicleUpdate'])->name('vehicleUpdate');
    Route::get('/deletevehicle/{id}', [VehicleController::class,'vehicleDelete'])->name('vehicleDelete');
    Route::get('/change-status-vehicle/{id}', [VehicleController::class,'vehicleStatusChange'])->name('vehicleStatusChange');
});


