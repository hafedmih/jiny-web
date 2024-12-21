<?php 

use App\Http\Controllers\Taxi\API\MoneyBackController;
use Illuminate\Support\Facades\Route;


Route::post('/money_back', [MoneyBackController::class,'moneyBack']);