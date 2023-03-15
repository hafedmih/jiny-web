<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\User;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\Settings;
use App\Transformers\Request\TripRequestTransformer;
use App\Traits\CommanFunctions;

class DriverArrivedController extends BaseController
{

    use CommanFunctions;

    protected $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    public function driverArrived(Request $request)
    {
        $request->validate([
            'request_id' => 'required',
            'driver_latitude' => 'required',
            'driver_longitude' => 'required'
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
        $arrived_pickup = Settings::where('name','auto_araive_radius_pickup')->first();
        if(is_null($arrived_pickup->value)){
            if (distanceBetweenTwoPoints($request->driver_latitude, $request->driver_longitude, 
           $request_detail->requestPlace->pick_lat, $request_detail->requestPlace->pick_lng) > 100) {
            return $this->sendError('Pickup is too far to arrive',[],403);
             }
        }else{
            if (distanceBetweenTwoPoints($request->driver_latitude, $request->driver_longitude, 
            $request_detail->requestPlace->pick_lat, $request_detail->requestPlace->pick_lng) > $arrived_pickup->value) {
                return $this->sendError('Pickup is too far to arrive',[],403);
            }
        }
        $this->validateRequest($request_detail,$user);


        $request_detail->update([
                'is_driver_arrived' => 1,
                'is_trip_start' => 0,
               // 'trip_start_time' => null,
                'arrived_at' => now()
            ]);

        $request_result =  fractal($request_detail, new TripRequestTransformer);

        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);
            
            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'driver-arrived');
            if(is_null($push_data)){
                $title = 'Driver Arrived';
                $body = 'Driver arrived on pickup location';
                $sub_title = 'Driver arrived on pickup location';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 

            $push_data = ['notification_enum'=>PushEnum::DRIVER_ARRIVED];

            dispatch(new SendPushNotification($title,$sub_title,$push_data, $userModel->device_info_hash, $userModel->mobile_application_type,0));


            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::DRIVER_ARRIVED;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);
    
            // $pushData = ['notification_enum' => PushEnum::DRIVER_ARRIVED, 'result' => (string) $request_result->toJson()];
           // $pushData = ['notification_enum' => PushEnum::DRIVER_ARRIVED, 'result' => $request_result];
           
        }
        
        return $this->sendResponse('Data Found', $request_result, 200);
    }

    
    public function driverArrivedDestination(Request $request)
    {
        $request->validate([
            'request_id' => 'required',
            'driver_latitude' => 'required',
            'driver_longitude' => 'required'
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
        $arrived_drop = Settings::where('name','auto_araive_radius_drop')->first();
       
        if(is_null($arrived_drop->value)){
            if (distanceBetweenTwoPoints($request->driver_latitude, $request->driver_longitude, $request_detail->requestPlace->pick_lat, $request_detail->requestPlace->pick_lng) > 100) {
                
               return $this->sendError('Pickup is too far to arrive',[],403);
           }

        }else{
            if (distanceBetweenTwoPoints($request->driver_latitude, $request->driver_longitude, $request_detail->requestPlace->pick_lat, $request_detail->requestPlace->pick_lng) > $arrived_drop->value) {
                
                return $this->sendError('Pickup is too far to arrive',[],403);
            }
        }

        $this->validateRequest($request_detail,$user);

        $request_detail->update([
                'is_driver_arrived' => 1,
                'arrived_at' => now()
            ]);

        $request_result =  fractal($request_detail, new TripRequestTransformer);

        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);
            
            $title = 'Driver Arrived';
            $body = 'Driver arrived on pickup location';
            $sub_title = 'Driver arrived on pickup location';

            $push_data = ['notification_enum'=>PushEnum::DRIVER_ARRIVED,'result'=>(string)$push_request_detail];


            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::DRIVER_ARRIVED;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);
    
            // $pushData = ['notification_enum' => PushEnum::DRIVER_ARRIVED, 'result' => (string) $request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::DRIVER_ARRIVED, 'result' => $request_result];
            dispatch(new SendPushNotification($title,$sub_title,$pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
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

        if ($request_detail->is_driver_arrived) {
            return $this->sendError('Already arrived',[],401);
        }

        if ($request_detail->is_completed) {
            return $this->sendError('Request Completed',[],401);
        }
        
        if ($request_detail->is_cancelled) {
            return $this->sendError('Request Cancelled',[],401);
        }
    }
}