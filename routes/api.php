<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth Api 
// require 'api/auth.php';
require __DIR__.'/boilerplate/api/languages.php';
require __DIR__.'/taxi/api/documents.php';
require __DIR__.'/taxi/api/complaints.php';
require __DIR__.'/taxi/api/sos.php';
require __DIR__.'/taxi/api/faq.php';
require __DIR__.'/taxi/api/promo.php';
require __DIR__.'/taxi/api/target.php';
require __DIR__.'/taxi/api/user.php';
require __DIR__.'/taxi/api/company.php';
require __DIR__.'/taxi/api/triphistory.php';
require __DIR__.'/taxi/api/requestrating.php';
require __DIR__.'/taxi/api/servicelocation.php';
require __DIR__.'/taxi/api/vehicle.php';
require __DIR__.'/taxi/api/driver.php';
require __DIR__.'/taxi/api/referral.php';
require __DIR__.'/taxi/api/errorlog.php';
require __DIR__.'/taxi/api/favourite.php';
require __DIR__.'/taxi/api/wallet.php';
require __DIR__.'/taxi/api/notification.php';
require __DIR__.'/taxi/api/requestInProgress.php';
require __DIR__.'/boilerplate/api/auth.php';
require __DIR__.'/taxi/api/request.php';
require __DIR__.'/taxi/api/subscription_master.php';
require __DIR__.'/taxi/api/dashboard.php';
require __DIR__.'/taxi/api/cancellation.php';
require __DIR__.'/taxi/api/cancellationlist.php';
require __DIR__.'/taxi/api/outstation.php';
require __DIR__.'/taxi/api/package.php';
require __DIR__.'/taxi/api/instantimageupload.php';
require __DIR__.'/taxi/api/invoicequestions.php';
require __DIR__.'/taxi/api/userdelete.php';
require __DIR__.'/taxi/api/individualpromomarketing.php';
require __DIR__.'/taxi/api/gohome.php';
require __DIR__.'/taxi/api/Payment.php';
require __DIR__.'/taxi/api/changepayment.php';
require __DIR__.'/taxi/api/updatedpaymentstatus.php';

use Salman\Mqtt\MqttClass\Mqtt;

use App\Jobs\NotifyViaMqtt;
Route::get('test', function () {

    $mqtt = new Mqtt();
    $output = $mqtt->ConnectAndPublish('test', "fbghdbj", 2);

    // dispatch(new NotifyViaMqtt('test', "hai", 1));
  });   