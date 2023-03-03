<?php 

use App\Http\Controllers\Taxi\Web\PromoCode\PromoController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/promocode', [PromoController::class,'Promolist'])->name('promolist');
    Route::get('/promocreate', [PromoController::class,'Promocreate'])->name('promocreate');
    Route::post('/promocreate', [PromoController::class,'Promosave'])->name('promosave');
    Route::post('/promoupdate', [PromoController::class,'PromoUpdate'])->name('PromoUpdate');
    Route::get('generate/promo',[PromoController::class,'generatePromo'])->name('generatePromo');
    Route::get('promo-edit/{id}', [PromoController::class,'promoEdit'])->name('promoEdit');
    Route::get('promo-delete/{id}', [PromoController::class,'promoDelete'])->name('promoDelete');
    Route::get('promo-active/{id}', [PromoController::class,'promoActive'])->name('promoActive');
});