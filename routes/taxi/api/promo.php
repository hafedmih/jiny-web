<?php 

use App\Http\Controllers\Taxi\API\PromoController;
use Illuminate\Support\Facades\Route;


Route::get('user/promocode', [PromoController::class,'PromoList']);
Route::post('user/promoapply', [PromoController::class,'PromoApply']);

