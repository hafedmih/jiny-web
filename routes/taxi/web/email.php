<?php 

use App\Http\Controllers\Taxi\Web\Email\EmailController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('email', [EmailController::class,'email'])->name('email');
    Route::post('email-add', [EmailController::class,'emailSave'])->name('emailSave');
     Route::get('email-delete/{slug}', [EmailController::class,'emailDelete'])->name('emailDelete');
     Route::get('email-new',[EmailController::class,'addemail'])->name('addemail');
    
});