<?php

use App\Http\Controllers\Taxi\Web\IndividualPromoMarketing\IndividualPromoMarketingController;
use Illuminate\Support\Facades\Route;


Route::group(['middelware' => ['auth','settings']], function (){

    Route::get('promo-marketing',[IndividualPromoMarketingController::class,'index'])->name('promo-Marketing');
    Route::get('promo-marketing-add', [IndividualPromoMarketingController::class,'promo'])->name('promo-Marketingadd');
     Route::post('promo-marketing-add', [IndividualPromoMarketingController::class,'promoStore'])->name('promo-MarketingSave');
     Route::get('/promo-marketing-edit/{id}', [IndividualPromoMarketingController::class,'promoEdit'])->name('promo-MarketingEdit');
     Route::post('/promo-marketing-update', [IndividualPromoMarketingController::class,'promoUpdate'])->name('promo-MarketingUpdate');
     Route::get('/promo-marketing-delete/{id}', [IndividualPromoMarketingController::class,'promoDelete'])->name('promo-MarketingDelete');
     Route::get('/change-status-promo-marketing/{id}', [IndividualPromoMarketingController::class,'promoStatusChange'])->name('promo-MarketingStatusChange');

});