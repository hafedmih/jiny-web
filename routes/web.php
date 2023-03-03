<?php

use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\taxi\Settings;
use App\Models\taxi\Requests\Request as RequestModel;

use App\Http\Controllers\Taxi\Web\Request\ShareTripController;
use App\Http\Controllers\Taxi\Web\ErrorLog\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // dispatch(new SendPushNotification('Title 😀',['message' => 'Test body 😀'],'token','android'));
    return redirect('/login');
});

// Route::get('pdf-generate', function () {
   
//   $settings = Settings::where('status',1)->pluck('value','name')->toArray();

//   $request_detail = RequestModel::where('id','f21fc993-9488-41e7-879b-c82df5241927')->first();
//   $pdf = \PDF::loadView('emails.RequestBillMailPDF',['settings' => $settings,'request_detail' => $request_detail]);
//   \Mail::to('karthikbackend.nplus@gmail.com')->send(new \App\Mail\MyTestMail($request_detail,$settings,$pdf));
//   // return view('emails.RequestBillMailPDF',['settings' => $settings,'request_detail' => $request_detail]);
//   return $pdf->download($request_detail->request_number.'.pdf');

// });

require __DIR__.'/auth.php';
require __DIR__.'/boilerplate/web/languages.php';
require __DIR__.'/boilerplate/web/version.php';
require __DIR__.'/boilerplate/web/rolePermission.php';
require __DIR__.'/taxi/web/complaint.php';
require __DIR__.'/boilerplate/web/user.php';
require __DIR__.'/boilerplate/web/company.php';
require __DIR__.'/taxi/web/usermanagement.php';
require __DIR__.'/taxi/web/documents.php';
require __DIR__.'/taxi/web/faq.php';
require __DIR__.'/taxi/web/sos.php';
require __DIR__.'/taxi/web/vehicle.php';
require __DIR__.'/taxi/web/driver.php';
require __DIR__.'/taxi/web/zone.php';
require __DIR__.'/taxi/web/target.php';
require __DIR__.'/taxi/web/promocode.php';
require __DIR__.'/taxi/web/settings.php';
require __DIR__.'/taxi/web/category.php';
require __DIR__.'/taxi/web/notification.php';
require __DIR__.'/taxi/web/dispatcher.php';
require __DIR__.'/taxi/web/fine.php';
require __DIR__.'/taxi/web/request.php';
require __DIR__.'/taxi/web/cancellation-reason.php';
require __DIR__.'/taxi/web/requestmanagement.php';
require __DIR__.'/taxi/web/faqlanguage.php';
require __DIR__.'/taxi/web/submaster.php';
require __DIR__.'/taxi/web/package.php';
require __DIR__.'/taxi/web/dashboard.php';
require __DIR__.'/taxi/web/outstationmaster.php';
require __DIR__.'/taxi/web/outofzone.php';
require __DIR__.'/boilerplate/web/country.php';
require __DIR__.'/taxi/web/vehiclemodel.php';
require __DIR__.'/taxi/web/profile.php';
require __DIR__.'/taxi/web/outstationmaster.php';
require __DIR__.'/taxi/web/reference.php';
require __DIR__.'/taxi/web/office.php';
require __DIR__.'/taxi/web/reports.php';
require __DIR__.'/taxi/web/outstationpackage.php';
require __DIR__.'/taxi/web/email.php';
require __DIR__.'/taxi/web/createdispatcherrequest.php';
require __DIR__.'/taxi/web/sms.php';
require __DIR__.'/boilerplate/web/2fa.php';
require __DIR__.'/taxi/web/invoicequestions.php';
require __DIR__.'/taxi/web/individual-promo-marketing.php';
require __DIR__.'/taxi/web/documentsgroup.php';
require __DIR__.'/taxi/web/driversummary.php';



Route::get('logs', [LogViewerController::class, 'index'])->name('loglist');
Route::get('share-view/{id}', [ShareTripController::class,'requestView']);

// use Salman\Mqtt\MqttClass\Mqtt;

// use App\Jobs\NotifyViaMqtt;
// Route::get('test', function () {

//     $mqtt = new Mqtt();
//     $output = $mqtt->ConnectAndPublish('test', "fbghdbj", 2);

//     // dispatch(new NotifyViaMqtt('test', "hai", 1));
//   });   


// use PhpMqtt\Client\Facades\MQTT;

// MQTT::publish('aa/test', 'Hello World!');


use App\Models\taxi\Requests\TripLog;
Route::get('test', function () {



    $post = new TripLog;

    $post->title = 'test';
    $post->body = 'body';
    $post->slug = 'slug';

    $post->save();

    return response()->json(["result" => "ok"], 201);
});
