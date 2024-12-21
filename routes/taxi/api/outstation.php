<?php 

use App\Http\Controllers\Taxi\API\OutstationController;
use Illuminate\Support\Facades\Route;



Route::get('outstation/list', [OutstationController::class,'outstationList']);
Route::post('outstation/eta', [OutstationController::class,'outstationEta']);

