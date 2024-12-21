<?php 

use App\Http\Controllers\Taxi\Web\CancellationReason\CancellationReasonController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('cancellation-reason', [CancellationReasonController::class,'index'])->name('cancellationReason');
    Route::post('cancellation-reason-add', [CancellationReasonController::class,'cancelReasonSave'])->name('cancelReasonSave');
    Route::get('cancellation-reason-edit/{id}', [CancellationReasonController::class,'cancelReasonEdit'])->name('cancelReasonEdit');
    Route::post('cancellation-reason-update', [CancellationReasonController::class,'cancelReasonUpdate'])->name('cancelReasonUpdate');
    Route::get('cancellation-reason-delete/{id}', [CancellationReasonController::class,'cancelReasonDelete'])->name('cancelReasonDelete');
    Route::get('cancellation-reason-change-status/{id}', [CancellationReasonController::class,'cancelReasonChangeStatus'])->name('cancelReasonChangeStatus');
});
