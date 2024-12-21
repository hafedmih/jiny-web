<?php 

use App\Http\Controllers\Taxi\API\CompanyController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'company', 'as'=>'company.','middleware' => ['api']], function () {
    Route::get('user', [CompanyController::class,'viewUser']);
   
});


