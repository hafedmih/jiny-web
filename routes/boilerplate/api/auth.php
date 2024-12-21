<?php 

use App\Http\Controllers\boilerplate\API\AuthAPIController;



Route::group(['prefix' => 'user', 'as'=>'user.'], function () {
    Route::post('/signin', [AuthAPIController::class,'login'])->name('user-signin');
    Route::post('/signin-with-username', [AuthAPIController::class,'loginwithUsernamePassword'])->name('user-signin-username');
    Route::get('/logout', [AuthAPIController::class,'logout'])->name('destroy');
    Route::post('/signup', [AuthAPIController::class,'register'])->name('user-signup');
    Route::post('/sendotp', [AuthAPIController::class,'sendOtp'])->name('user-otp');
});


Route::post('auth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
