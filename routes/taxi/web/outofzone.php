<?php 

use App\Http\Controllers\Taxi\Web\Outofzone\OutofzoneController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {

    Route::get('outofzonelist', [OutofzoneController::class,'outofzoneMaster'])->name('outofzone-master');
    Route::post('outofzonesave', [OutofzoneController::class,'outofzoneSave'])->name('outofzonesave');
    Route::post('outofzoneupdate', [OutofzoneController::class,'outofzoneUpdate'])->name('outofzoneupdate');
    Route::get('outofzoneedit/{id}', [OutofzoneController::class,'outofzoneEdit'])->name('outofzoneedit');
    Route::get('outofzonedelete/{id}', [OutofzoneController::class,'outofzoneDelete'])->name('outofzonedelete');
    Route::get('outofzonestatus-active/{id}', [OutofzoneController::class,'outofzoneChangeStatus'])->name('outofzoneactive');
});