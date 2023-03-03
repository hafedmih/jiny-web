<?php 

use App\Http\Controllers\Taxi\Web\Sos\SosController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('sos-management', [SosController::class,'sos'])->name('sos-management');
    Route::post('sos-management-add', [SosController::class,'sosSave'])->name('sos-managementSave');
    Route::get('sos-management-edit/{key}', [SosController::class,'sosEdit'])->name('sos-managementEdit');
    Route::post('sos-management-update', [SosController::class,'sosUpdate'])->name('sos-managementUpdate');
    Route::get('sos-management-delete/{key}', [SosController::class,'sosDelete'])->name('sos-managementDelete');
    Route::get('sos-management-view/{id}', [SosController::class,'sosView'])->name('sos-managementView');
    Route::get('sos-management-change-status/{id}', [SosController::class,'sosChangeStatus'])->name('sos-managementChangeStatus');
});
