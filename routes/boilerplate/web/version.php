
<?php 

use App\Http\Controllers\boilerplate\Web\ProjectVersion\ProjectVersionController;

Route::group(['prefix' => 'versions', 'as'=>'versions.', 'middleware' => ['auth']], function () {
    Route::get('/', [ProjectVersionController::class,'index'])->name('index');
    Route::get('/create', [ProjectVersionController::class,'getVersionCode'])->name('create');
    Route::post('/store', [ProjectVersionController::class,'store'])->name('store');
    Route::get('/{slug}/edit', [ProjectVersionController::class,'edit'])->name('edit');
    Route::post('/{slug}/edit', [ProjectVersionController::class,'update'])->name('update');
    // Route::get('/{id}/delete', [ProjectVersionController::class,'destroy'])->name('destroy');
    Route::get('/{id}/baned', [ProjectVersionController::class,'banned'])->name('banned');
});
