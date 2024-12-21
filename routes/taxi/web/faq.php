<?php 

use App\Http\Controllers\Taxi\Web\Faq\FaqController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('faq-management', [FaqController::class,'faq'])->name('faq-management');
    Route::post('faq-management-add', [FaqController::class,'faqSave'])->name('faq-managementSave');
    Route::post('faq-management-ad', [FaqController::class,'faqLanguage'])->name('faq-managementLanguage');
    Route::get('faq-management-edit/{id}', [FaqController::class,'faqEdit'])->name('faq-managementEdit');
    Route::post('faq-management-update', [FaqController::class,'faqUpdate'])->name('faq-managementUpdate');
   // Route::get('faq-management-delete/{id}', [FaqController::class,'faqDelete'])->name('faq-managementDelete');
    Route::get('faq-management-view/{id}', [FaqController::class,'faqView'])->name('faq-managementView');
    Route::get('faq-management-change-status/{id}', [FaqController::class,'faqChangeStatus'])->name('faq-managementChangeStatus');  
    Route::get('faq-management-delete/{id}', [FaqController::class,'destroy'])->name('faq-managementDelete');
});

