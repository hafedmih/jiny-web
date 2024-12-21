<?php 

use App\Http\Controllers\Taxi\API\ErrorlogController;
use Illuminate\Support\Facades\Route;


// Route::get('errorlog', [ErrorlogController::class,'errorlog']);
Route::group(['prefix' => 'errorlog', 'as'=>'errorlog.','middleware' => ['api']], function () {
    Route::post('', [ErrorlogController::class,'index']);
});