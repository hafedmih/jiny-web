<?php 

use App\Http\Controllers\Taxi\Web\Target\TargetController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('target', [TargetController::class,'Targetlist'])->name('targetlist');
    Route::post('targetdriver', [TargetController::class,'TargetDrivers'])->name('TargetDrivers');
    Route::get('target-delete/{id}', [TargetController::class,'targetDelete'])->name('targetDelete');
    Route::get('target-active/{id}', [TargetController::class,'targetActive'])->name('targetActive');
    Route::post('target-add', [TargetController::class,'TargetSave'])->name('targetSave');
    Route::get('target/driver', [TargetController::class,'TargetDriverGet'])->name('TargetDriverGet');
});