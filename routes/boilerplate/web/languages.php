<?php 

use App\Http\Controllers\boilerplate\Web\LanguageTranslationController;
use Illuminate\Support\Facades\Route;


Route::get('languages', [LanguageTranslationController::class,'index'])->name('languages');
Route::get('language', [LanguageTranslationController::class,'viewLanguage'])->name('viewLanguage');
Route::post('language-save', [LanguageTranslationController::class,'saveLanguage'])->name('saveLanguage');
Route::post('language-update', [LanguageTranslationController::class,'updateLanguage'])->name('updateLanguage');
Route::get('language-edit/{id}', [LanguageTranslationController::class,'editLanguage'])->name('editLanguage');
Route::get('language-delete/{id}', [LanguageTranslationController::class,'deleteLanguage'])->name('deleteLanguage');
Route::get('language-active/{id}', [LanguageTranslationController::class,'activeLanguage'])->name('activeLanguage');
Route::post('translations/update', [LanguageTranslationController::class,'transUpdate'])->name('translation.update.json');
Route::post('translations/updateKey', [LanguageTranslationController::class,'transUpdateKey'])->name('translation.update.json.key');
Route::delete('translations/destroy/{key}', [LanguageTranslationController::class,'destroy'])->name('translations.destroy');
// add mobile view 
Route::post('translations/m_update', [LanguageTranslationController::class,'m_transUpdate'])->name('translation.update.mobile.json');
Route::post('translations/m_updateKey', [LanguageTranslationController::class,'m_transUpdateKey'])->name('translation.update.mobile.json.key');
Route::delete('translations/m_destroy/{key}', [LanguageTranslationController::class,'m_destroy'])->name('translations.mobile.destroy');
//
Route::post('translations/create', [LanguageTranslationController::class,'store'])->name('translations.create');
Route::get('check-translation', function(){
	\App::setLocale('fr');
	
	dd(__('website'));
});
