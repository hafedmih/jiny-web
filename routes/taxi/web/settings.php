<?php 

use App\Http\Controllers\Taxi\Web\Setting\SettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('settings', [SettingsController::class,'settings'])->name('settings');
    Route::post('settings-save', [SettingsController::class,'settingsSave'])->name('settingsSave');
});


