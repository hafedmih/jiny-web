<?php 

use App\Http\Controllers\boilerplate\Web\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;


Route::get('user-complaints', [ComplaintController::class,'userComplaints'])->name('userComplaints');
Route::get('complaints', [ComplaintController::class,'complaints'])->name('complaints');
Route::get('complaints-edit/{id}', [ComplaintController::class,'complaintsEdit'])->name('complaintsEdit');
Route::get('complaints-delete/{id}', [ComplaintController::class,'complaintsDelete'])->name('complaintsDelete');
Route::get('complaints-active/{id}', [ComplaintController::class,'complaintsActive'])->name('complaintsActive');
Route::post('complaints-add', [ComplaintController::class,'complaintsSave'])->name('complaintsSave');
Route::post('complaints-update', [ComplaintController::class,'complaintsUpdate'])->name('complaintsUpdate');