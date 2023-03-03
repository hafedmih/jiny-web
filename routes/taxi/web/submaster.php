<?php 

use App\Http\Controllers\Taxi\Web\Submaster\SubmasterController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('sublist', [SubmasterController::class,'Submasterlist'])->name('sublist');
    Route::post('submastersave', [SubmasterController::class,'submasterSave'])->name('submastersave');
    Route::post('submasterupdate', [SubmasterController::class,'submasterUpdate'])->name('submasterupdate');
    Route::get('submasteredit/{slug}', [SubmasterController::class,'submasterEdit'])->name('submasteredit');
 Route::get('submasterdelete/{slug}', [SubmasterController::class,'submasterDelete'])->name('submasterdelete');
    // Route::get('/request', [RequestController::class,'request'])->name('request');
    // Route::get('/promocreate', [PromoController::class,'Promocreate'])->name('promocreate');
    // Route::post('/promocreate', [PromoController::class,'Promosave'])->name('promosave');
    // Route::get('generate/promo',[PromoController::class,'generatePromo'])->name('generatePromo');
    // Route::get('promo-delete/{id}', [PromoController::class,'promoDelete'])->name('promoDelete');
    // Route::get('request-view/{id}', [RequestController::class,'requestView'])->name('requestView');
});