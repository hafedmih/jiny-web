<?php 

use App\Http\Controllers\Taxi\API\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'user', 'as'=>'user.'], function () {
    Route::get('profile', [UserController::class,'viewUser']);
    Route::post('profile', [UserController::class,'updateProfile']);
    Route::post('check/phonenumber', [UserController::class,'CheckPhoneNumber']);

    
    Route::post('logout',[UserController::class,'logoutApi']);
    Route::post('profile/language', [UserController::class,'userlanguage']);
});


