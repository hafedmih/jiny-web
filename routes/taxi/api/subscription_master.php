<?php 

use App\Http\Controllers\Taxi\API\SubscriptionMasterController;
use Illuminate\Support\Facades\Route;


Route::get('subscription/list', [SubscriptionMasterController::class,'subscriptionList']);

Route::post('subscription/add', [SubscriptionMasterController::class,'subscriptionAdd']);
