<?php 

use App\Http\Controllers\Taxi\API\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'complaints', 'as'=>'complaints.', 'middleware' => ['api']], function () {
    Route::get('list', [ComplaintController::class,'complaintsList']);
    Route::get('trip-list', [ComplaintController::class,'tripComplaintsList']);
    Route::post('/add', [ComplaintController::class,'complaintsUserAdd']);
    Route::get('/history/{slug}', [ComplaintController::class,'userComplaintsList']);
});


Route::group(['prefix' => 'suggestions', 'as'=>'suggestions.', 'middleware' => ['api']], function () {
    Route::get('list', [ComplaintController::class,'suggestionList']);
});