<?php 

use App\Http\Controllers\Taxi\Web\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('category', [CategoryController::class,'category'])->name('category');
    Route::post('category-add', [CategoryController::class,'categorySave'])->name('categorySave');
    Route::get('category-edit/{id}', [CategoryController::class,'categoryEdit'])->name('categoryEdit');
    Route::post('category-update', [CategoryController::class,'categoryUpdate'])->name('categoryUpdate');
    Route::get('category-delete/{id}', [CategoryController::class,'categoryDelete'])->name('categoryDelete');
    Route::get('category-change-status/{id}', [CategoryController::class,'categoryChangeStatus'])->name('categoryChangeStatus');
});
