<?php

use App\Http\Controllers\Taxi\API\CancelRequest\DriverCancelRequestController;
use App\Http\Controllers\Taxi\API\CancelRequest\UserCancelRequestController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'request/cancel','middleware' => ['api']],function() {
    Route::post('user', [UserCancelRequestController::class,'cancelRequest']);

    Route::post('driver', [DriverCancelRequestController::class,'cancelRequest']);
});