<?php 

use App\Http\Controllers\Taxi\Web\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('user-complaints', [ComplaintController::class,'userComplaints'])->name('userComplaints');
    Route::get('complaints', [ComplaintController::class,'complaints'])->name('complaints');
    Route::get('complaints-edit/{key}', [ComplaintController::class,'complaintsEdit'])->name('complaintsEdit');
    Route::get('complaints-delete/{id}', [ComplaintController::class,'complaintsDelete'])->name('complaintsDelete');
    Route::get('complaints-active/{id}', [ComplaintController::class,'complaintsActive'])->name('complaintsActive');
    Route::post('complaints-add', [ComplaintController::class,'complaintsSave'])->name('complaintsSave');
    Route::post('complaints-update', [ComplaintController::class,'complaintsUpdate'])->name('complaintsUpdate');

    Route::get('trip-complaints', [ComplaintController::class,'tripComplaint'])->name('tripComplaint');
    Route::post('trip-complaints-add', [ComplaintController::class,'tripComplaintsave'])->name('tripComplaintsave');
    Route::post('trip-complaints-update', [ComplaintController::class,'tripcomplaintsUpdate'])->name('tripcomplaintsUpdate');
    Route::get('trip-complaints-edit/{key}', [ComplaintController::class,'tripcomplaintsEdit'])->name('tripcomplaintsEdit');
    Route::get('trip-complaints-delete/{id}', [ComplaintController::class,'tripcomplaintsDelete'])->name('tripcomplaintsDelete');
    Route::get('trip-complaints-active/{id}', [ComplaintController::class,'tripcomplaintsActive'])->name('tripcomplaintsActive');
});





