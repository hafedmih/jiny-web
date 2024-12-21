<?php 

use App\Http\Controllers\Taxi\Web\InvoiceQuestions\InvoiceQuestionsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
     Route::get('questions', [InvoiceQuestionsController::class,'vehicle'])->name('questions');
     Route::get('questions-add', [InvoiceQuestionsController::class,'index'])->name('questions-add');
     Route::post('question-add', [InvoiceQuestionsController::class,'questionsStore'])->name('questionsSave');
     Route::get('/editquestions/{id}', [InvoiceQuestionsController::class,'questionsEdit'])->name('questionsEdit');
     Route::post('/questionsUpdate', [InvoiceQuestionsController::class,'questionsUpdate'])->name('questionsUpdate');
     Route::get('/delete-questions/{id}', [InvoiceQuestionsController::class,'questionsDelete'])->name('questionsDelete');
     Route::get('/change-status-questions/{id}', [InvoiceQuestionsController::class,'questionsStatusChange'])->name('questionsStatusChange');
});
