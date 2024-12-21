<?php 

use App\Http\Controllers\Taxi\API\GoHomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'gohome', 'as'=>'gohome.','middleware' => ['api']], function () {
    Route::get('/', [GoHomeController::class,'GoHomeList']);
    Route::post('/', [GoHomeController::class,'store'])->name('store');
    Route::post('/enabled', [GoHomeController::class,'GohomeEnable'])->name('GohomeEnable');
});