<?php 

use App\Http\Controllers\Taxi\Web\Request\RequestController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/request', [RequestController::class,'request'])->name('request');
    Route::get('/cancel-request', [RequestController::class,'CancelRequest'])->name('CancelRequest');
    Route::get('/cancel-delete-request', [RequestController::class,'CancelDeleteRequest'])->name('CancelDeleteRequest');
    // Route::post('/promocreate', [PromoController::class,'Promosave'])->name('promosave');
    // Route::get('generate/promo',[PromoController::class,'generatePromo'])->name('generatePromo');
    Route::get('request-end/{ride}', [RequestController::class,'requestEnd'])->name('requestEnd');
    Route::get('request-view/{id}', [RequestController::class,'requestView'])->name('requestView');
    Route::get('request-views/{id}', [RequestController::class,'requestViews'])->name('requestViews');
    Route::post('request-category-change', [RequestController::class,'requestCategoryChange'])->name('requestCategoryChange');

    Route::get('/requests_later', [RequestController::class,'requests_later'])->name('requests_later');
    Route::get('/requests_rental_now', [RequestController::class,'requests_rental_now'])->name('requests_rental_now');
    Route::get('/requests_rental_later', [RequestController::class,'requests_rental_later'])->name('requests_rental_later');
    Route::get('/outstation_list', [RequestController::class,'outstation_list'])->name('outstation_list');
    Route::get('/cancelled_trips', [RequestController::class,'cancelled_trips'])->name('cancelled_trips');
    Route::get('/on_going_trips', [RequestController::class,'on_going_trips'])->name('on_going_trips');
});