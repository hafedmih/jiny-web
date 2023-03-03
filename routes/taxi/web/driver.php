<?php 

use App\Http\Controllers\Taxi\Web\Driver\DriverController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('driver', [DriverController::class,'driver'])->name('driver');
    Route::get('driver-add', [DriverController::class,'driverAdd'])->name('driverAdd');
    Route::post('driver-add', [DriverController::class,'driverSave'])->name('driverSave');
    Route::get('/driver-edit/{slug}', [DriverController::class,'driverEdit'])->name('driverEdit');
    Route::post('/driver-update', [DriverController::class,'driverUpdate'])->name('driverUpdate');
    Route::get('/driver-delete/{slug}', [DriverController::class,'driverDelete'])->name('driverDelete');
    Route::get('/change-status-driver/{slug}', [DriverController::class,'driverActive'])->name('driverActive');
    Route::get('/driver-refered-list/{slug}', [DriverController::class,'driverRefernceList'])->name('driverRefernceList');
    Route::get('/driver-complaints-list/{slug}', [DriverController::class,'driverComplaintsList'])->name('driverComplaintsList');
    Route::get('/driver-ratings-list/{slug}', [DriverController::class,'driverRatingsList'])->name('driverRatingsList');
    Route::get('/driver-fine-list/{slug}', [DriverController::class,'driverFineList'])->name('driverFineList');
    Route::get('/driver-trip-details/{slug}', [DriverController::class,'driverTripDetails'])->name('driverTripDetails');
    Route::get('/driver-details/{slug}', [DriverController::class,'driverDetails'])->name('driverDetails');
    Route::get('/driver/working-hours/{slug}', [DriverController::class,'driverWorkingHours'])->name('driverWorkingHours');
    Route::get('/driver/working-hours', [DriverController::class,'DriverLogsLists'])->name('DriverLogsLists');
    Route::get('/driver-document-edit/{user}/{slug}', [DriverController::class,'driverDocumentEdit'])->name('driverDocumentEdit');
    Route::post('/driver-document-update', [DriverController::class,'driverDocumentUpdate'])->name('driverDocumentUpdate');
    Route::post('/driver-document-approved', [DriverController::class,'driverDocumentApproved'])->name('driverDocumentApproved'); 
    Route::post('driverfine/add/{slug}', [DriverController::class,'fineSave'])->name('fineSave');
    Route::get('driver/get/models/{slug}', [DriverController::class,'DriverGetModel'])->name('DriverGetModel');
    Route::get('driver-logs', [DriverController::class,'driverLogs'])->name('driverLogs');



});


