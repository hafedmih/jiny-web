<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Taxi\API\Dispatcher\DispatcherController;

Route::post('dispacher/login', [DispatcherController::class,'login']);


// Route::group(['prefix' => 'dispacher', 'as'=>'complaints.', 'middleware' => ['api']], function () {
Route::group(['prefix' => 'dispacher', 'as'=>'complaints.'], function () {
    Route::get('customers/{number}', [DispatcherController::class,'getCustomer']);
    Route::post('grt-vehicles', [DispatcherController::class,'getVehicles']);
    Route::post('/create-trip', [DispatcherController::class,'createDispatchRequest']);
    Route::get('/history/{status}', [DispatcherController::class,'dispatcherTripList']);
    Route::get('/trip-details/{ride}', [DispatcherController::class,'dispatchRequestView']);
});
