<?php 

use App\Http\Controllers\Taxi\Web\Notification\NotificationController;
use App\Http\Controllers\Taxi\Web\Notification\PushNotificationTranslationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('notification', [NotificationController::class,'notification'])->name('notification');
    Route::get('notification-add', [NotificationController::class,'notificationAdd'])->name('notificationAdd');
    Route::post('notification-add', [NotificationController::class,'notificationSave'])->name('notificationSave');
    Route::get('notification-delete/{id}', [NotificationController::class,'notificationDelete'])->name('notificationDelete');
    
});
 
Route::group(['prefix' => 'push/translation','middleware' => ['auth','settings']], function () {

    Route::get('', [PushNotificationTranslationController::class,'list'])->name('push-transaltion-list');
    Route::get('edit/{id}', [PushNotificationTranslationController::class,'edit'])->name('push-transaltion-edit');
    Route::post('update', [PushNotificationTranslationController::class,'update'])->name('push-transaltion-update');
    Route::post('save', [PushNotificationTranslationController::class,'save'])->name('push-transaltion-save');
    Route::get('delete/{slug}', [PushNotificationTranslationController::class,'delete'])->name('push-transaltion-delete');
 
});


