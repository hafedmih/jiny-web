<?php 

use App\Http\Controllers\Taxi\Web\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/dashboard', [DashboardController::class,'dashboard'])->name('dashboard');

    Route::get('/dashboard-total-trips/{value}', [DashboardController::class,'dashboardTotalTrips'])->name('dashboardTotalTrips');

    Route::get('/dashboard-amount-transaction/{value}', [DashboardController::class,'dashboardAmountTransaction'])->name('dashboardAmountTransaction');

    Route::get('/dashboard-zone-trips/{value}', [DashboardController::class,'dashboardZoneTrips'])->name('dashboardZoneTrips');

    Route::get('/dashboard-cancel-trips/{value}', [DashboardController::class,'dashboardCancelTrips'])->name('dashboardCancelTrips');

    Route::get('/gendrate-map-token', [DashboardController::class,'gendrateMapToken'])->name('gendrateMapToken');

    Route::get('/notify', [DashboardController::class,'notify'])->name('notify');

});