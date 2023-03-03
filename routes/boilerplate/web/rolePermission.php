<?php 

use App\Http\Controllers\boilerplate\Web\RolePermission\RolesController;
use App\Http\Controllers\boilerplate\Web\RolePermission\PermissionsController;

/* Manage Role */
Route::group(['prefix' => 'roles', 'as'=>'roles.', 'middleware' => ['auth']], function () {
    Route::get('/', [RolesController::class,'index'])->name('index');
    Route::get('/create', [RolesController::class,'create'])->name('create');
    Route::post('/create', [RolesController::class,'store'])->name('store');
    Route::get('/{slug}/edit', [RolesController::class,'edit'])->name('edit');
    Route::post('/{slug}/edit', [RolesController::class,'update'])->name('update');
    Route::get('/{slug}/delete', [RolesController::class,'destroy'])->name('destroy');
    Route::get('/get-my-roles', [RolesController::class,'getRoles'])->name('get-my-roles');
    Route::get('/get-roles', [RolesController::class,'getRoleKeys'])->name('get-roles');
    
    Route::post('/assign/{slug}/role', [RolesController::class,'roleAssign'])->name('assign-role');
    Route::get('/{slug}', [RolesController::class,'rolePermission'])->name('permissions');

    Route::get('/{role_slug}/{permission_name}', [RolesController::class,'updateRolePermission'])->name('update-permission');
    Route::post('/{slug}', [RolesController::class,'updateRolePermission'])->name('update-role-permission');

});

/* Manage Permission */
Route::group(['prefix' => 'permissions', 'as'=>'permissions.', 'middleware' => ['auth']], function () {
    Route::get('/', [PermissionsController::class,'index'])->name('index');
    Route::get('/create', [PermissionsController::class,'create'])->name('create');
    Route::post('/create', [PermissionsController::class,'store'])->name('store');
    Route::get('/{id}/edit', [PermissionsController::class,'edit'])->name('edit');
    Route::post('/{id}/edit', [PermissionsController::class,'update'])->name('update');
    Route::get('/{id}/delete', [PermissionsController::class,'destroy'])->name('destroy');
});







