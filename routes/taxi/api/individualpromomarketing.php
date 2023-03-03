<?php 

use App\Http\Controllers\Taxi\API\PromoMarketingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'promo/marketing', 'as'=>'promo/marketing.','middleware' => ['api']], function () {
    Route::get('/', [PromoMarketingController::class,'promoMarketingList']);   
    Route::post('promo-code/', [PromoMarketingController::class,'promoMarketing']);
});