<?php 

use App\Http\Controllers\Taxi\API\InvoiceQuestionsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'invoice/question', 'as'=>'invoice/question.','middleware' => ['api']], function () {
    Route::get('/', [InvoiceQuestionsController::class,'InvoiceQuestionsList']);   
});