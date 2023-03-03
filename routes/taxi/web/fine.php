<?php 

use App\Http\Controllers\Taxi\Web\Fine\FineController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
  
    Route::get('/fine', [FineController::class,'fine'])->name('fine');
    Route::post('/fine-update',[FineController::class,'fineUpdate'])->name('fineUpdate');
    Route::post('/fine-store',[FineController::class,'fineSavefunction'])->name('driverfineSave');
});