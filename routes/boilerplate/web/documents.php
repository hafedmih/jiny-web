<?php 

use App\Http\Controllers\boilerplate\Web\Document\DocumentController;
use Illuminate\Support\Facades\Route;


Route::get('documents', [DocumentController::class,'index'])->name('documents');
Route::get('documents-edit/{id}', [DocumentController::class,'documentsEdit'])->name('documentsEdit');
Route::get('documents-delete/{id}', [DocumentController::class,'documentsDelete'])->name('documentsDelete');
Route::get('documents-active/{id}', [DocumentController::class,'documentsActive'])->name('documentsActive');
Route::post('documents-add', [DocumentController::class,'documentsSave'])->name('documentsSave');
Route::post('documents-update', [DocumentController::class,'documentsUpdate'])->name('documentsUpdate');