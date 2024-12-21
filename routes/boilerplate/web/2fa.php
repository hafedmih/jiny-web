<?php 

use App\Http\Controllers\boilerplate\Web\TwoFAController\TwoFAController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('2fa', [TwoFAController::class, 'index'])->name('2fa.index');
    Route::post('2fa', [TwoFAController::class, 'store'])->name('2fa.post');
    Route::get('2fa/reset', [TwoFAController::class, 'resend'])->name('2fa.resend');
});