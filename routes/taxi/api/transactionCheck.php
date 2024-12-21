<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Taxi\API\CheckTransactionController;

Route::post('check_transaction', [CheckTransactionController::class,'checkTransaction']);