<?php 

use App\Http\Controllers\Taxi\API\TargetController;
use Illuminate\Support\Facades\Route;


Route::get('target', [TargetController::class,'TargetList']);
