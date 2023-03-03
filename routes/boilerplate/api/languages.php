<?php 

use App\Http\Controllers\boilerplate\API\LanguageTranslationAPIController;
use Illuminate\Support\Facades\Route;


Route::post('languages', [LanguageTranslationAPIController::class,'index'])->name('languages1');
Route::get('languages/{code}', [LanguageTranslationAPIController::class,'sendjsonfile']);
