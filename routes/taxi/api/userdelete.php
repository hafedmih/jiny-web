<?php 

use App\Http\Controllers\Taxi\API\UserRemoveController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'userdelete', 'as'=>'userdelete.','middleware' => ['api']], function () {
    
    Route::get('delete/', [UserRemoveController::class,'userDelete']);
});