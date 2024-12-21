<?php

use App\Http\Controllers\Taxi\API\ServiceLocationController;
use Illuminate\Support\Facades\Route;

Route::get('servicelocation/list', [
    ServiceLocationController::class,
    'ServiceLocationList',
]);
Route::post('checkzone', [ServiceLocationController::class, 'checkzone']);
Route::post('check/outstation', [
    ServiceLocationController::class,
    'checkoutstation',
])->name('checkoutstation');
