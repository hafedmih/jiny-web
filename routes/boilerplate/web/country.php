<?php 

use App\Http\Controllers\boilerplate\Web\CountryController;
use Illuminate\Support\Facades\Route;


Route::get('country', [CountryController::class,'List'])->name('country');
Route::get('country-active/{id}', [CountryController::class,'activeCountry'])->name('activeCountry');
