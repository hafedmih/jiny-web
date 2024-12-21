<?php

use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\taxi\Settings;
use App\Models\taxi\Requests\Request as RequestModel;

use App\Http\Controllers\Taxi\Web\Request\ShareTripController;
use App\Http\Controllers\Taxi\Web\ErrorLog\LogViewerController;
use App\Http\Controllers\Taxi\Web\Delete\DeleteController;

//use App\Models\taxi\Settings;


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
Route::get('/test1', function () {
    $pickup_lat = '18.099533';
    $pickup_long = '-16.013473';
    $drop_lat = '18.150872';
    $drop_long = '-15.932945';
    $settings_distance = Settings::where('name', 'distance_matrix')->first();

    $url =
        'https://maps.googleapis.com/maps/api/directions/json?origin=' .
        $pickup_lat .
        ',' .
        $pickup_long .
        '&destination=' .
        $drop_lat .
        ',' .
        $drop_long .
        '&mode=driving&language=pl-PL&key=' .
        $settings_distance->value .
        '&alternatives=true';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
//return $response_a;
    $shortestdistance = 0;
    if (count($response_a['routes']) != 1) {
        foreach ($response_a['routes'] as $routes) {
            $distwithkm = $routes['legs'][0]['distance']['text'];
            $aa = explode(' ', $distwithkm);
            if ($aa[1] == 'm') {
                $newdist = 0;
            } else {
                $newdist = (float) substr($distwithkm, 0, 4);
            }
            $shortestdistance = $newdist;
            if ($shortestdistance > $newdist) {
                $shortestdistance = $newdist;
            }
        }
        return $shortestdistance;
    } else {
        $distwithkm = $response_a['routes'][0]['legs'][0]['distance']['text'];
        $aa = explode(' ', $distwithkm);
        if ($aa[1] == 'm') {
            $newdist = 0;
        } else {
            $newdist = (float) substr($distwithkm, 0, 4);
        }
        return $newdist;
    }
});
Route::get('/', function () {
    // dispatch(new SendPushNotification('Title 😀',['message' => 'Test body 😀'],'token','android'));
    return redirect('/login');
});
Route::get('/show-duplicate-requests', function () {
    // Execute the ShowDuplicateRequests command
    $output = Artisan::output('show:duplicate-requests');

    // Return the output to a view
    return view('show-duplicate-requests', ['output' => $output]);
});
Route::get('/delete-duplicate-requests', function () {
    // Execute the DeleteDuplicateRequests command
    $output = Artisan::call('delete:duplicate-requests');

    // Return a message indicating the success or failure of the command
    return "Duplicate requests affected to drivers deleted successfully.";
});

Route::get('/get-pincode/{phone}', function ($phone) {
    // Execute the query to retrieve the pincode based on the provided phone number
    $pincode = DB::table('drivers')
                ->join('users', 'drivers.user_id', '=', 'users.id')
                ->where('users.phone_number', $phone)
                ->select('drivers.pincode')
                ->first();

    // Check if pincode exists
    if ($pincode) {
        return response()->json(['pincode' => $pincode->pincode]);
    } else {
        return response()->json(['error' => 'Pincode not found for the provided phone number.'], 404);
    }
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
Route::get('delete-account', [DeleteController::class, 'index'])->name('userslist');
Route::post('delete-accounts', [DeleteController::class, 'destroy'])->name('users.destroy');

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
