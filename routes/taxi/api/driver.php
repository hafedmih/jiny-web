<?php 

use App\Http\Controllers\Taxi\API\DriverController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver', 'as'=>'driver.','middleware' => ['api']], function () {
    Route::post('/signin', [DriverController::class,'driverLogin'])->name('driver-signin');
    Route::post('/signup', [DriverController::class,'driversignup']);
    Route::get('profile', [DriverController::class,'viewUser']);
    Route::post('profile', [DriverController::class,'updateProfile']);
    Route::post('check/phonenumber', [DriverController::class,'CheckPhoneNumber']);
    Route::post('/document-upload', [DriverController::class,'updateDriverDocument']);
    Route::get('/online-update', [DriverController::class,'updateDriverOnline']);
});

