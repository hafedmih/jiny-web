<?php 

use App\Http\Controllers\Taxi\Web\Office\OfficeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('otp', [OfficeController::class,'otp'])->name('showOtp');
    
});


