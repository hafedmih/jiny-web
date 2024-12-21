<?php 

use App\Http\Controllers\Taxi\Web\Document\DocumentsGroupController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('group-documents', [DocumentsGroupController::class,'index'])->name('group-documents');
    Route::get('group-documents-edit/{id}', [DocumentsGroupController::class,'documentsEdit'])->name('group-documentsEdit');
    Route::get('group-documents-delete/{id}', [DocumentsGroupController::class,'documentsDelete'])->name('group-documentsDelete');
    Route::get('group-documents-active/{id}', [DocumentsGroupController::class,'documentsActive'])->name('group-documentsActive');
    Route::post('group-documents-add', [DocumentsGroupController::class,'documentsSave'])->name('group-documentsSave');
    Route::post('group-documents-update', [DocumentsGroupController::class,'documentsUpdate'])->name('group-documentsUpdate');
});