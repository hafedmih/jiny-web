<?php 

use App\Http\Controllers\Taxi\Web\Request\RequestManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('requestlist', [RequestManagementController::class,'index'])->name('requestlist');
    

});