<?php 

use App\Http\Controllers\Taxi\API\RequestInProgressController;
use Illuminate\Support\Facades\Route;


Route::get('user/request_in_progress', [RequestInProgressController::class,'userRequestInProgressController']);

Route::get('driver/request_in_progress', [RequestInProgressController::class,'driverRequestInProgressController']);
