<?php 

use App\Http\Controllers\Taxi\Web\VehicleModel\VehicleModelController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
     Route::get('vehicle-model', [VehicleModelController::class,'vehicle'])->name('vehiclemodel');
     Route::get('vehicle-model-add', [VehicleModelController::class,'vehiclemodel'])->name('vehiclemodeladd');
     Route::post('vehicle-model-add', [VehicleModelController::class,'vehicleModelStore'])->name('vehiclemodelSave');
     Route::get('/editvehicle-model/{id}', [VehicleModelController::class,'vehicleModelEdit'])->name('vehiclemodelEdit');
     Route::post('/vehicle-ModelUpdate', [VehicleModelController::class,'vehicleModelUpdate'])->name('vehiclemodelUpdate');
     Route::get('/deletevehicle-model/{id}', [VehicleModelController::class,'vehicleModelDelete'])->name('vehicleModelDelete');
     Route::get('/change-status-vehicle-model/{id}', [VehicleModelController::class,'vehicleModelStatusChange'])->name('vehicleModelStatusChange');
});
