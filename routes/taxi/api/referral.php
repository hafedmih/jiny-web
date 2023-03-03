<?php 

use App\Http\Controllers\Taxi\API\ReferralController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'get', 'as'=>'get.','middleware' => ['api']], function () {
    Route::get('/referral', [ReferralController::class,'getReferraldriver']);
    Route::get('/user/referral', [ReferralController::class,'getReferraluser']);

  
});

