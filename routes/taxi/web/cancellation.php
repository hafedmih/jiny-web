<?php 

use App\Http\Controllers\Taxi\Web\CancellationReason\CancellationReasonController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cancellations','middleware' => ['auth','settings']], function () {
    Route::get('/', [CancellationReasonController::class,'index'])->name('index');
    Route::post('add', [CancellationReasonController::class,'save'])->name('save');
    Route::get('edit/{id}', [CancellationReasonController::class,'edit'])->name('edit');
    Route::post('update', [CancellationReasonController::class,'update'])->name('update');
    Route::get('delete/{id}', [CancellationReasonController::class,'delete'])->name('delete');
    Route::get('change-status/{id}', [CancellationReasonController::class,'changeStatus'])->name('changestatus');
});
