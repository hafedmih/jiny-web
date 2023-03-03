<?php 

use App\Http\Controllers\Taxi\API\VehicleController;
use Illuminate\Support\Facades\Route;


Route::get('types/list', [VehicleController::class,'typeList']);

Route::post('get/types', [VehicleController::class,'getTypes']);

Route::post('get/model', [VehicleController::class,'vehicleModelList']);
