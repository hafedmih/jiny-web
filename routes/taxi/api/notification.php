<?php 

use App\Http\Controllers\Taxi\API\NotificationController;
use Illuminate\Support\Facades\Route;


Route::get('notification/list', [NotificationController::class,'notificationList']);

