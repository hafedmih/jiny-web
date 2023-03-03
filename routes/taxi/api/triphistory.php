<?php

use App\Http\Controllers\Taxi\API\TripHistoryController;
use Illuminate\Support\Facades\Route;
use App\Models\taxi\Requests\Request as RequestModel;

Route::group(['prefix' => 'request'],function() {
// Trip History
    Route::post('user/trip/history', [TripHistoryController::class,'TripHistory']);
    Route::post('single/trip/history', [TripHistoryController::class,'singleTripHistory']);
});