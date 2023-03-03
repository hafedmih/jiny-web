<?php

use App\Http\Controllers\Taxi\API\Request\CreateRequestController;
use App\Http\Controllers\Taxi\API\Request\DriverArrivedController;
use App\Http\Controllers\Taxi\API\Request\DriverEndRequestController;
use App\Http\Controllers\Taxi\API\Request\DriverStartTripController;
use App\Http\Controllers\Taxi\API\Request\RequestAcceptRejectController;
use App\Http\Controllers\Taxi\API\Request\ChangeLocationController;
use App\Http\Controllers\Taxi\API\Request\CreateRideLaterController;
use App\Http\Controllers\Taxi\API\Request\PassengerUploadImagesController;
use App\Http\Controllers\Taxi\API\Request\SingleTripHistoryController;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'request', 'as'=>'request.'], function () {
    Route::post('create', [CreateRequestController::class,'createRequest'])->name('newrequest');
    // Accet/Reject Request
    Route::post('respond', [RequestAcceptRejectController::class,'respondTripRequest'])->name('respond');

    Route::post('arrive', [DriverArrivedController::class,'driverArrived'])->name('arrived');

    Route::post('start', [DriverStartTripController::class,'driverStartTrip'])->name('started');

    Route::post('end', [DriverEndRequestController::class,'endRequest'])->name('endTrip');
    
    Route::post('change-location', [ChangeLocationController::class,'index'])->name('endTrips');

    Route::post('approve-change-location', [ChangeLocationController::class,'driverApprove'])->name('approveLocation');

    Route::get('fetch-driver-location',[ChangeLocationController::class,'fetchDiverDistance'])->name('fetchDriverLocation');

    Route::post('arrive-destination', [DriverArrivedController::class,'driverArrivedDestination'])->name('arriveds');

    Route::post('image-upload', [PassengerUploadImagesController::class,'passengerUploadImages'])->name('passengerUploadImages');

    Route::post('skip-upload', [PassengerUploadImagesController::class,'skipUploadImages'])->name('skipUploadImages');

    Route::post('single-history', [SingleTripHistoryController::class,'SingleTripHistoryList'])->name('singleTripHistory');

    Route::post('retake-image', [PassengerUploadImagesController::class,'retakeImage'])->name('retakeImage');


});