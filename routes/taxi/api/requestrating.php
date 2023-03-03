<?php 

use App\Http\Controllers\Taxi\API\RequestRatingController;
use Illuminate\Support\Facades\Route;


Route::get('request/rating', [RequestRatingController::class,'RequestList']);
Route::post('request/rating', [RequestRatingController::class,'store'])->name('store');