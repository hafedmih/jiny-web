<?php 

use App\Http\Controllers\Taxi\API\RiderAddressController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'rider', 'as'=>'rider.','middleware' => ['api']], function () {
    Route::post('/address', [RiderAddressController::class,'rideraddress']);

});