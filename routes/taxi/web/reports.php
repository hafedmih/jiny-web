<?php 

use App\Http\Controllers\Taxi\Web\Reports\ReportsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','settings']], function () {
    Route::get('reports', [ReportsController::class,'reports'])->name('showreports');
    Route::get('tripReports', [ReportsController::class,'tripReports'])->name('showtripReports');
    Route::get('tripWiseReports', [ReportsController::class,'tripWiseReports'])->name('tripWiseReports');
    Route::get('transaction-list', [ReportsController::class,'transactionReports'])->name('transactionReportslist');
    Route::get('driver-wallet', [ReportsController::class,'driverWallet'])->name('driverWallet');
    Route::get('questions-Reports', [ReportsController::class,'requestQuestions'])->name('questionsReports');
    Route::get('questions-driver-reports/{id}', [ReportsController::class,'driverQuestions'])->name('driverQuestionsReports');
    Route::post('income-reports',[ReportsController::class,'incomeReports'])->name('incomeReports');
    Route::get('total-income-reports',[ReportsController::class,'totalIncomeReports'])->name('totalincomeReports');
    Route::get('user-reports',[ReportsController::class,'userReports'])->name('userReports');
    Route::get('income-list/{value}',[ReportsController::class,'incomeList'])->name('incomeList');
    Route::get('promo-use-list',[ReportsController::class,'promoUseList'])->name('promoUseList');
    
    Route::get('card-payment-list',[ReportsController::class,'paymentList'])->name('card-payment-List');
  
    
});

