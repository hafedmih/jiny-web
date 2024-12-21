<?php 

use App\Http\Controllers\boilerplate\Web\CompanyManagement\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('company', [CompanyController::class,'company'])->name('company');
    Route::get('company-edit/{slug}', [CompanyController::class,'companyEdit'])->name('companyEdit');
    Route::get('company-delete/{slug}', [CompanyController::class,'companyDelete'])->name('companyDelete');
    Route::get('company-active/{slug}', [CompanyController::class,'companyActive'])->name('companyActive');
    Route::post('company-add', [CompanyController::class,'companySave'])->name('companySave');
    Route::post('company-update', [CompanyController::class,'companyUpdate'])->name('companyUpdate');
    Route::post('company-password-update', [CompanyController::class,'companyPasswordUpdate'])->name('companyPasswordUpdate');
});