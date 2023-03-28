<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Taxi\API\Dispatcher\DispatcherController;

Route::post('dispacher/login', [DispatcherController::class,'login']);