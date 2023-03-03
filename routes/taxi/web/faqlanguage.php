<?php 

use App\Http\Controllers\Taxi\Web\Faq\FaqlanguageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {   
   // Route::post('faq-management', [FaqlanguageController::class,'save'])->name('faq-language-Save');
});