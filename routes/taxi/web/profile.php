<?php 

use App\Http\Controllers\Taxi\Web\Profile\ProfileController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
     Route::get('/profile', [ProfileController::class,'Profile'])->name('profile');
    //  Route::get('/profileUpdate', [ProfileController::class,'profileUpdate'])->name('profileupdate');
     Route::get('/profileedit/{slug}', [ProfileController::class,'profileEdit'])->name('profileedit');
     Route::post('/profileupdate',[ProfileController::class,'profileUpdate'])->name('profileupdate');
     Route::post('/passwordchanging', [ProfileController::class,'passwordChange'])->name('passwordchanging');
    // Route::get('promo-active/{id}', [PromoController::class,'promoActive'])->name('promoActive');
});