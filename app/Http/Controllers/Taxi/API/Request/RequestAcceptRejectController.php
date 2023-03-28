<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\CancelMethod;
use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Taxi\API\Request\AcceptRejectRequest;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Driver;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\User;
use App\Models\taxi\Requests\RequestDriverLog;
use App\Transformers\Request\TripRequestTransformer;
use DB;
use App\Models\taxi\Settings;
use App\Traits\CommanFunctions;
use phpseclib3\Crypt\EC\Formats\Keys\Common;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RequestAcceptRejectController extends BaseController
{
    use CommanFunctions;

    protected $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    public function respondTripRequest(AcceptRejectRequest $request)
    {
        try{
            
            $clientlogin = $this::getCurrentClient(request());
        
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);
            
            $driver = $user->driver;
           //  dd($user->device_info_hash);
            if (!$user->hasRole('driver')) {
                return $this->sendError('Unauthorized',[],401);    
            }
            
            $request_detail = $this->request->where('id', $request->request_id)->first();
            if(is_null($request_detail)){
                return $this->sendError('Wrong Request',[],404);  
            }

            if($request_detail->is_driver_started == 1){
                return $this->sendError('Alerady Driver Accepted this Trip',[],404);  
            }
            $this->validateRequestDetail($request_detail,$user);

            $totalAccepted = $driver->total_accept;
            $totalRejected = $driver->total_reject;

            if ($request->is_accept == 1) {
                $message = 'trip_accepted';
                // Update Driver to the trip request detail
                // dd($user);

                RequestDriverLog::create([
                    'request_id' => $request_detail->id,
                    'user_id' => $user->id,
                    'driver_lat' => $request->driver_latitude,
                    'driver_lng' => $request->driver_longitude,
                    'date_time' => NOW(),
                    'type' => 'ACCEPT',
                    'user_type' => 'DRIVER',
                    'status' => 1
                ]);

                $updated_params = [
                    'driver_id'         => $user->id,
                    'accepted_at'       => now(),
                    'is_driver_started' => 1,
                    'hold_status'       => 0
                ];

                $request_detail->update($updated_params);
                
                $totalAccepted += 1;
                // Update the driver's available state as false
                $driver->is_available = false;
                $driver->total_accept = $totalAccepted;
                $driver->reject_count = 0;
                $driver->save();

                $request_result =  fractal($request_detail, new TripRequestTransformer);

                $meta_drivers = RequestMeta::where('request_id', $request->request_id)->get();

                foreach ($meta_drivers as $key => $value) {
                    if($value->driver_id != $user->id){
                        $driverModel = User::find($value->driver_id);
                        $title = Null;
                        $body = '';
                        $lang = $driverModel->language;
                        $push_data = $this->pushlanguage($lang,'trip-accept');
                        if(is_null($push_data)){
                            $title = 'Already driver accepted trip';
                            $body = 'The Driver is already accepted this trip';
                            $sub_title = 'The Driver is already accepted this trip';
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title =  $push_data->description;
                        } 
                        $push_data = ['notification_enum'=>PushEnum::ALREADY_DRIVER_ACCEPT_TRIP];
                        // Form a socket sturcture using users'id and message with event name
                        $socket_data = new \stdClass();
                        $socket_data->success = true;
                        $socket_data->success_message  = PushEnum::ALREADY_DRIVER_ACCEPT_TRIP;
                        $socket_data->result = $request_result;
                        $socketData = ['event' => 'already_driver_accept_trip_'.$driverModel->slug,'message' => $socket_data];
                        sendSocketData($socketData);
                        // dispatch(new SendPushNotification($title,$sub_title, $push_data, $driverModel->device_info_hash, $driverModel->mobile_application_type,0));
                        sendPush($title,$sub_title, $push_data, $driverModel->device_info_hash, $driverModel->mobile_application_type,0);
                    }
                }
                // dd($request_detail);
                $this->deleteMetaRecords($request);
                
                if ($request_detail->user_id != null) {

                    $push_request_detail = $request_result->toJson();
                    $userModel = User::find($request_detail->user_id);
                    $driverModel = User::find($request_detail->driver_id);

                    $car_details = Driver::where('user_id',$user->id)->first();
                    // dd($car_details)s;
                    //dd($userModel->device_info_hash);

                    // $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$userModel->phone_number.'&message=Hi '.$userModel->firstname.', your booking is confirmed with us. Your OTP for the ride is '.$request_detail->request_otp.'. Driver name : '.$driverModel->firstname.' Phone number: '.$driverModel->phone_number.' Vehicle Number: '.$car_details->car_number.' . Thank you for using our taxi service . Our City Our Taxi !!! - NPTECH.');

                    
                    $title = Null;
                    $body = '';
                    $lang = $userModel->language;
                    $push_data = $this->pushlanguage($lang,'trip-accept');
                    if(is_null($push_data)){
                        $title = 'Trip Request Accepted';
                        $body = 'The Driver is on the way to pickup';
                        $sub_title = 'The Driver is on the way to pickup';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    } 

                    $push_data = ['notification_enum'=>PushEnum::TRIP_ACCEPTED_BY_DRIVER];


                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::TRIP_ACCEPTED_BY_DRIVER;
                    $socket_data->result = $request_result;

                    $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    sendPush($title,$sub_title, $push_data, $userModel->device_info_hash, $userModel->mobile_application_type,0);
                    // dispatch(new SendPushNotification($title,$sub_title, $push_data, $userModel->device_info_hash, $userModel->mobile_application_type,0));

            
                    // $pushData = ['notification_enum' => PushEnum::TRIP_ACCEPTED_BY_DRIVER, 'result' => (string) $request_result->toJson()];
                    // $pushData = ['notification_enum' => PushEnum::TRIP_ACCEPTED_BY_DRIVER, 'result' => $request_result];
                    // dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,1));
                }

                return $this->sendResponse('Data Found', $request_result, 200);
            }else{
                $message = 'trip_rejected';

                RequestDriverLog::create([
                    'request_id' => $request_detail->id,
                    'user_id' => $user->id,
                    'driver_lat' => $request->driver_latitude,
                    'driver_lng' => $request->driver_longitude,
                    'date_time' => NOW(),
                    'type' => 'REJECT',
                    'user_type' => 'DRIVER',
                    'status' => 1
                ]);

                // $updated_params = [
                //     'driver_id'         => null,
                //     'accepted_at'       => null,
                //     'is_driver_started' => 0
                // ];

                // $request_detail->update($updated_params);

                if($request_detail->manual_trip == 'MANUAL'){
                    // $request_detail->cancelled_at = NOW();
                    // $request_detail->is_cancelled = 1;
                    // $request_detail->cancel_method = 'Driver';
                    $request_detail->hold_status = 0;
                    $request_detail->driver_id = NULL;
                    $request_detail->save();

                    return $this->sendResponse('Data Found', $request_detail, 200);
                }
                $request_result =  fractal($request_detail, new TripRequestTransformer);
                
                $totalRejected += 1;

                $driver->total_reject += 1;
                $driver->reject_count += 1;
                $driver->save();

                $user->trips_count = 0;
                $user->save();

                //LAUNCH PURPOSE ONLY

                // Driver blocked for trip rejected
                // $driver_trip_cancel = Settings::where('name',"driver_block_trip_reject")->first();
                // $driver_trip_cancel = $driver_trip_cancel ? (int)$driver_trip_cancel->value : 0;
                // if($driver->reject_count == $driver_trip_cancel){
                //     $user->active = 0;
                //     $user->block_reson = "Driver Reject Multi Trips";
                //     $user->save();
                //     $title = Null;
                //     $body = '';
                //     $lang = $user->language;
                //     $push_data = $this->pushlanguage($lang,'driver-blocked');
                //     if(is_null($push_data)){
                //        $title = 'Driver Your Account Is Blocked';
                //        $body = 'Your rejected multiple trips for continue. So, your account is blocked. Please, contact admin.';
                //     }else{
                //         $title = $push_data->title;
                //         $body =  $push_data->description;
                //     }
                //     dispatch(new SendPushNotification("Driver Your Account Is Blocked",['message' => "Your rejected multiple trips for continue. So, your account is blocked. Please, contact admin.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
                // }

                // Delete Driver record from meta table
                RequestMeta::where('request_id', $request->request_id)->where('driver_id', $user->id)->delete();
                // Send request to next driver
                $request_meta = RequestMeta::where('request_id', $request->request_id)->first();

                if ($request_meta) {
                    // $request_meta->update(['active'=>true]);
                    // // Send push notification like create request to the driver
                    // $title = 'New Trip Requested 😊️';
                    // $body = 'Hi!! New Order Please Accept the trip request';
                    // $sub_title = 'Hi!! New Order Please Accept the trip request';

                    // // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$request_result->toJson()];
                    // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
                    
                    // $notifiable_driver = User::find($request_meta->driver_id);
                    // // dd($request_meta->driver_id);
                    // // $driver = Driver::where('user_id',$notifiable_driver->user_id)->first();;

                    // $socket_data = new \stdClass();
                    // $socket_data->success = true;
                    // $socket_data->success_message  = PushEnum::REQUEST_CREATED;
                    // $socket_data->result = $request_result;
                    
                    // $socketData = ['event' => 'request_'.$notifiable_driver->slug,'message' => $socket_data];
                    // sendSocketData($socketData);

                    // dispatch(new SendPushNotification($title,$sub_title, $pushData, $notifiable_driver->device_info_hash, $notifiable_driver->mobile_application_type,1));

                } else {
                    $request_result =  fractal($request_detail, new TripRequestTransformer);

                    // Cancell the request as automatic cancel state
                    if($request_detail->is_later == 0 && !$request_detail->driver_id && $request_detail->is_cancelled == 0){
                        $request_detail->update([
                            'is_cancelled'=>true,
                            'cancel_method'=>CancelMethod::AUTOMATIC_TEXT,
                            'cancelled_at'=>date('Y-m-d H:i:s')
                        ]);


                        if ($request_detail->user_id != null) {
                            // Send push notification as no-driver-found to the user
                            $userModel = User::find($request_detail->user_id);
                            if(is_null($userModel)){
                                return $this->sendError('Wrong user','failure.'.$e,403);  
                            }

                            $title = Null;
                            $body = '';
                            $lang = $userModel->language;
                            $push_data = $this->pushlanguage($lang,'no-driver');
                            if(is_null($push_data)){
                                $title = 'No Driver Found Around You 🙁️';
                                $body = 'Sorry please try again after some times,there is no driver available for your ride now';
                                $sub_title = 'Sorry please try again after some times,there is no driver available for your ride now';

                            }else{
                                $title = $push_data->title;
                                $body =  $push_data->description;
                                $sub_title =  $push_data->description;

                            } 

                            // $pushData = ['notification_enum'=>PushEnum::NO_DRIVER_FOUND,'result'=>(string)$request_result->toJson()];
                            $pushData = ['notification_enum'=>PushEnum::NO_DRIVER_FOUND,'result'=>$request_result];
                            
                            dispatch(new SendPushNotification($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
                            
                            // Form a socket sturcture using users'id and message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message  = PushEnum::NO_DRIVER_FOUND;
                            $socket_data->result = $request_result;
                            
                            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
                            sendSocketData($socketData);
                        }
                    }
                }
            }
            // dd($driver);
            $totalTrips = $totalAccepted + $totalRejected;
            $acceptanceRatio = ($totalAccepted * 100) / $totalTrips;
            $driver->acceptance_ratio = $acceptanceRatio;
            $driver->save();

           
            return $this->sendResponse('Data Found', $request_result, 200);
        } catch (\Exception $e) {
            DB::rollback(); 
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    /**
    * Delete All Meta driver's records
    */
    public function deleteMetaRecords(Request $request)
    {
        RequestMeta::where('request_id', $request->request_id)->delete();
    }

    public function validateRequestDetail($request_detail,$user)
    {
        if ($request_detail->is_driver_started && $request_detail->driver_id != $user->driver->id) {
            return $this->sendError('Request accepted by another driver',[],401);
        }

        if ($request_detail->is_completed) {
            return $this->sendError('request completed already',[],401);
        }

        if ($request_detail->is_cancelled) {
            return $this->sendError('request cancelled',[],401);
        }
    }
}
