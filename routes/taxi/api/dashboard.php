<?php 

use App\Http\Controllers\Taxi\API\DashboardController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'dashboard', 'as'=>'dashboard.','middleware' => ['api']], function () {
    Route::get('', [DashboardController::class,'DashboardList']);
});


Route::group(['middleware' => ['api']], function () {
    Route::get('customer-care', [DashboardController::class,'customerCare']);
});