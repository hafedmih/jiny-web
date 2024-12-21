<?php 

use App\Http\Controllers\Taxi\Web\Sms\SmsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('sms', [SmsController::class,'sms'])->name('sms');
    Route::post('sms-add', [SmsController::class,'smsSave'])->name('smsSave');
    Route::get('sms-delete/{slug}', [SmsController::class,'smsDelete'])->name('smsDelete');
    
});