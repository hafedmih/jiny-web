<?php 

use App\Http\Controllers\Taxi\API\PackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Taxi\API\checkotpController;



Route::get('rental/list', [PackageController::class,'packageList']);
Route::post('rental/eta', [PackageController::class,'packageEta']);
Route::get('check', [checkotpController::class,'checkotp']);


