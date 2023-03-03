<?php 

use App\Http\Controllers\Taxi\API\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'create', 'as'=>'create.','middleware' => ['api']], function () {
    Route::post('/order', [PaymentController::class,'OrderGenerate']);

});