<?php 

use App\Http\Controllers\Taxi\API\FaqController;
use Illuminate\Support\Facades\Route;


Route::get('faq/', [FaqController::class,'faqList']);
// Route::get('faq/{slug}', [FaqController::class,'test']);

// Route::get('faq', [FaqController::class,'faqList']);


