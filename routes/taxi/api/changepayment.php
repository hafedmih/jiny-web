<?php 

use App\Http\Controllers\Taxi\API\ChangePaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'change', 'as'=>'change.','middleware' => ['api']], function () {
    Route::post('/payment', [ChangePaymentController::class,'ChangePayment']);

});