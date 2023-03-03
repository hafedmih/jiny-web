<?php 

use App\Http\Controllers\Taxi\API\WalletController;
use Illuminate\Support\Facades\Route;


Route::get('wallet', [WalletController::class,'walletList']);
Route::post('wallet/add-amount', [WalletController::class,'store'])->name('stores');