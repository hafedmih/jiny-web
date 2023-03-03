<?php 

use App\Http\Controllers\boilerplate\Web\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('users', [UserController::class,'user'])->name('user');
    Route::get('users-edit/{slug}', [UserController::class,'usersEdit'])->name('usersEdit');
    Route::get('users-delete/{slug}', [UserController::class,'usersDelete'])->name('usersDelete');
    Route::get('users-active/{slug}', [UserController::class,'usersActive'])->name('usersActive');
    Route::post('users-add', [UserController::class,'usersSave'])->name('usersSave');
    Route::post('users-update', [UserController::class,'usersUpdate'])->name('usersUpdate');
    Route::post('users-password-update', [UserController::class,'usersPasswordUpdate'])->name('usersPasswordUpdate');
});