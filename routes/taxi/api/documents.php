<?php 

use App\Http\Controllers\Taxi\API\DocumentController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'document', 'as'=>'document.','middleware' => ['api']], function () {
    Route::get('', [DocumentController::class,'documentsList']);
    Route::get('document-group', [DocumentController::class,'documentsgroupList']);
});
