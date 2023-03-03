<?php 

use App\Http\Controllers\Taxi\Web\Dispatcher\DispatcherController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/dispatcher', [DispatcherController::class,'dispatcher'])->name('dispatcher');
    Route::get('/dispatcher/edit/{ride}', [DispatcherController::class,'dispatcherEdit'])->name('dispatcherEdit');
    Route::get('/get_customer/{number}', [DispatcherController::class,'getCustomer'])->name('getCustomer');
    Route::get('/get-customer-detail/{slug}', [DispatcherController::class,'getCustomerDetails'])->name('getCustomerDetails');
    Route::get('/get-driver-detail/{slug}/{number}', [DispatcherController::class,'getDriverDetails'])->name('getDriverDetails');
    Route::post('/get_vehicle', [DispatcherController::class,'getVehicles'])->name('getVehicles');

    Route::post('/create-dispatch-request', [DispatcherController::class,'createDispatchRequest'])->name('createDispatchRequest');
    Route::post('/edit-dispatch-request', [DispatcherController::class,'editDispatchRequest'])->name('editDispatchRequest');
    Route::get('/dispatch-request-view/{ride}', [DispatcherController::class,'dispatchRequestView'])->name('dispatchRequestView');
    Route::get('/dispatch-request-cancel/{ride}', [DispatcherController::class,'dispatchTripCancel'])->name('dispatchTripCancel');
    Route::get('/get-dispatch-request/{ride}', [DispatcherController::class,'getDispatchRequest'])->name('getDispatchRequest');
    Route::get('/search-driver/{ride}', [DispatcherController::class,'searchDriver'])->name('searchDriver');
    Route::get('/assign-driver-trip/{ride}/{driver}', [DispatcherController::class,'assignDriver'])->name('assignDriver');
    
    Route::get('/dispatcher-trip-list', [DispatcherController::class,'dispatcherTripList'])->name('dispatcherTripList');

    Route::get('/get-rental-package-items/{slug}', [DispatcherController::class,'getRentalPackage'])->name('getRentalPackage');
    Route::get('/get-rental-package-items-eta', [DispatcherController::class,'getRentalPackageEta'])->name('getRentalPackageEta');
    Route::get('/get-outstation-eta', [DispatcherController::class,'getOutstationEta'])->name('getOutstationEta');
    Route::get('/get-outstation-location/{name}', [DispatcherController::class,'getOutstationLocation'])->name('getOutstationLocation');
    Route::get('/admin-trip-cancel/{trip}', [DispatcherController::class,'adminTripCancel'])->name('adminTripCancel');

});