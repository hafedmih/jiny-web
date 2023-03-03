<?php 
use App\Http\Controllers\Taxi\Web\UserManagement\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('user-management', [UserManagementController::class,'userManage'])->name('userManage');
    Route::get('usermanagement-edit/{slug}', [UserManagementController::class,'usermanagementEdit'])->name('usermanagementEdit');
    Route::get('usermanagement-delete/{slug}', [UserManagementController::class,'usermanagementDelete'])->name('usermanagementDelete');
    Route::get('usermanagement-active/{slug}', [UserManagementController::class,'usermanagementActive'])->name('usermanagementActive');
    Route::post('usermanagement-add', [UserManagementController::class,'usermanagementSave'])->name('usermanagementSave');
    Route::post('usermanagement-update', [UserManagementController::class,'usermanagementUpdate'])->name('usermanagementUpdate');

    #Route::get('/user-management-delete/{slug}', [UserManagementController::class,'userDelete'])->name('userDelete');
    Route::get('/change-status-user-management/{slug}', [UserManagementController::class,'userActive'])->name('userActive');
    Route::get('/user-management-view/{slug}', [UserManagementController::class,'userView'])->name('userView');
    Route::get('/user-trip-history/{slug}', [UserManagementController::class,'userTripsList'])->name('userTripsList');
    Route::post('userwallet/add', [UserManagementController::class,'walletSave'])->name('walletSave');
    Route::post('userwallet/add-amount', [UserManagementController::class,'walletSaveAmount'])->name('walletSaveAmount');
    Route::get('/user-wallet/{slug}', [UserManagementController::class,'userWallet'])->name('userWallet');
    Route::get('/user-complaints-list/{slug}', [UserManagementController::class,'userComplaintsList'])->name('userComplaintsList');
    Route::get('/user-rating/{slug}', [UserManagementController::class,'userRatingsList'])->name('userRatingsList');
    Route::get('/user-fine/{slug}',[UserManagementController::class,'userFineList'])->name('userFineList');
    Route::get('/user-referal/{slug}',[UserManagementController::class,'userRefernceList'])->name('userreferal');


});