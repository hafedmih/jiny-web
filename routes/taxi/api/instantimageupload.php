<?php 

use App\Http\Controllers\Taxi\API\InstantImageUploadController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instant/image/upload', 'as'=>'instantimageupload.','middleware' => ['api']], function () {
    Route::post('/', [InstantImageUploadController::class,'instantimageupload'])->name('instantimageupload');
});