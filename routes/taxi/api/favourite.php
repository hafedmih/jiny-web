<?php 

use App\Http\Controllers\Taxi\API\FavouriteController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'favourite', 'as'=>'favourite.','middleware' => ['api']], function () {
    Route::get('/', [FavouriteController::class,'FavouriteList']);
    Route::post('/', [FavouriteController::class,'store'])->name('store');
    Route::post('edit/{slug}', [FavouriteController::class,'edit'])->name('edit');
    Route::post('delete/{slug}', [FavouriteController::class,'destroy'])->name('destroy');
});