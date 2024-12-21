<?php 

use App\Http\Controllers\Taxi\API\UpdatedPaymentStatusController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'update/payment', 'as'=>'change.','middleware' => ['api']], function () {
    Route::post('/status', [UpdatedPaymentStatusController::class,'UpdatePaymentStatus']);

});