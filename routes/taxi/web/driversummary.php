<?php 

use App\Http\Controllers\Taxi\Web\Request\DriverSummaryController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('/driver-summary', [DriverSummaryController::class,'SummaryView'])->name('SummaryView');
    Route::get('/completed-local-trip', [DriverSummaryController::class,'CompletedLocalView'])->name('CompletedLocalView');
    Route::get('/completed-rental-trip', [DriverSummaryController::class,'CompletedRentalView'])->name('CompletedRentalView');
    Route::get('/completed-outstation-trip', [DriverSummaryController::class,'CompletedOutstationView'])->name('CompletedOutstationView');
});