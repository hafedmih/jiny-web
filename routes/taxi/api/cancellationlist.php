<?php 

use App\Http\Controllers\Taxi\API\CancellationReasonListController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cancellation', 'as'=>'cancellation.','middleware' => ['api']], function () {
    Route::post('list', [CancellationReasonListController::class,'cancellationReason']);
});