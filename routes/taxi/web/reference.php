<?php 

use App\Http\Controllers\Taxi\Web\Reference\ReferenceController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/reference', [ReferenceController::class,'reference'])->name('reference');

    Route::get('/heatmap', [ReferenceController::class,'mapView'])->name('heatmap');

    // Route::get('/dashboard-amount-transaction/{value}', [DashboardController::class,'dashboardAmountTransaction'])->name('dashboardAmountTransaction');

    // Route::get('/dashboard-zone-trips/{value}', [DashboardController::class,'dashboardZoneTrips'])->name('dashboardZoneTrips');

    // Route::get('/dashboard-cancel-trips/{value}', [DashboardController::class,'dashboardCancelTrips'])->name('dashboardCancelTrips');
});