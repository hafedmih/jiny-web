<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\User;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\OutstationUploadImages;
use App\Traits\CommanFunctions;


class DriverStartTripController extends BaseController
{

    use CommanFunctions;

    
    protected $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    public function driverStartTrip(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'pick_lat'  => 'required',
            'pick_lng'  => 'required',
        ]);

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('driver')) {
            return $this->sendError('Unauthorized',[],401);    
        }

        $request_detail = $this->request->where('id', $request->request_id)->first();

        if($request->has('request_otp')){
            if($request_detail->request_otp != $request->request_otp) return $this->sendError('OTP is invalid. Plesae enter correct OTP',[],403);
        }
        
        if($request_detail->trip_type == 'OUTSTATION' ){
            if($request->has('trip_image') && $request->trip_image != "" && $request->has('start_km') && $request->start_km != ""){
                $filename =  uploadImage('images/outstation',$request->file('trip_image'));
                OutstationUploadImages::create([
                    'request_id' => $request_detail->id,
                    'user_id' => $user->id,
                    'trip_start_km_image' => $filename,
                    'trip_start_km' => $request->start_km
                ]);

                $requests = RequestModel::where('id',$request->request_id)->first();

                $user_slug_get = User::where('id',$requests->user_id)->first();

                $out_station_upload = OutstationUploadImages::where('request_id',$request->request_id)->first();

                $title     = Null;
                $body      = '';
                $title     = 'Trip Start Kilometer Upload Successfully';
                $body      = 'Trip Start Kilometer Upload Successfully';
                $sub_title = 'Trip Start Kilometer Upload Successfully';

         
                $pushData = ['notification_enum' => PushEnum::KILOMETER_START];


                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = "";
                $socket_data->result = ['trip_start_km' => $out_station_upload->trip_start_km_image,'trip_km' => $out_station_upload->trip_start_km];
                $socketData = ['event' => 'kilometer_upload_'.$user_slug_get->slug,'message' => $socket_data];
                sendSocketData($socketData);

                dispatch(new SendPushNotification($title,$sub_title, $pushData, $user_slug_get->device_info_hash, $user_slug_get->mobile_application_type,0));


            }
            else{
                return $this->sendError('Trip start km and image is required',[],401);
            }
        }
        if($request->has('trip_image') && $request->trip_image != "" && $request->has('start_km') && $request->start_km != ""){
            if($request_detail->trip_type == 'RENTAL' ){
                if($request->has('trip_image') && $request->trip_image != "" && $request->has('start_km') && $request->start_km != ""){
                    $filename =  uploadImage('images/outstation',$request->file('trip_image'));
                    OutstationUploadImages::create([
                        'request_id' => $request_detail->id,
                        'user_id' => $user->id,
                        'trip_start_km_image' => $filename,
                        'trip_start_km' => $request->start_km
                    ]);

                    $requests = RequestModel::where('id',$request->request_id)->first();

                    $user_slug_get = User::where('id',$requests->user_id)->first();
    
                    $out_station_upload = OutstationUploadImages::where('request_id',$request->request_id)->first();


                    $title     = Null;
                    $body      = '';
                    $title     = 'Trip Start Kilometer Upload Successfully';
                    $body      = 'Trip Start Kilometer Upload Successfully';
                    $sub_title = 'Trip Start Kilometer Upload Successfully';
    
             
                    $pushData = ['notification_enum' => PushEnum::KILOMETER_START];


                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = "";
                    $socket_data->result = ['trip_start_km' => $out_station_upload->trip_start_km_image,'trip_km' => $out_station_upload->trip_start_km];
                    $socketData = ['event' => 'kilometer_upload_'.$user_slug_get->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                   dispatch(new SendPushNotification($title,$sub_title, $pushData, $user_slug_get->device_info_hash, $user_slug_get->mobile_application_type,0));


                }
                else{
                    return $this->sendError('Trip start km and image is required',[],401);
                }
            }
        }

        
        $this->validateRequest($request_detail,$user);

        $request_detail->update([
                'is_trip_start' => 1,
                'trip_start_time' => now()
            ]);

        $request_place = $request_detail->requestPlace;
        $request_place->pick_lat = $request->pick_lat;
        $request_place->pick_lng = $request->pick_lng;
        $request_place->save();

        $request_result =  fractal($request_detail, new TripRequestTransformer);
        
        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);
            
            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'trip-started');
            if(is_null($push_data)){
                $title = 'Trip Started';
                $body = 'Trip Started By Driver';
                $sub_title = 'Trip Started By Driver';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 

            $pushData = ['notification_enum'=>PushEnum::DRIVER_STARTED_THE_TRIP];
            
           // $push_data = ['notification_enum'=>PushEnum::DRIVER_STARTED_THE_TRIP,'result'=>(string)$push_request_detail];
            // dd($push_data);
            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::DRIVER_STARTED_THE_TRIP;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);

            dispatch(new SendPushNotification($title, $sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
    
            // $pushData = ['notification_enum' => PushEnum::DRIVER_STARTED_THE_TRIP, 'result' => (string) $request_result->toJson()];
          
        }
        
        return $this->sendResponse('Data Found', $request_result, 200);
    }

    /**
    * Validate Request
    */
    public function validateRequest($request_detail,$user)
    {
        if ($request_detail->driver_id != $user->driver->id) {
            return $this->sendError('Unauthorized',[],401);
        }

        if ($request_detail->is_trip_start) {
            return $this->sendError('Already trip started',[],401);
        }

        if ($request_detail->is_completed) {
            return $this->sendError('Request Completed',[],401);
        }
        
        if ($request_detail->is_cancelled) {
            return $this->sendError('Request Cancelled',[],401);
        }
    }
}