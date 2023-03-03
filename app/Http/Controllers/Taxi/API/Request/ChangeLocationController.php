<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\PushEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\User;
use App\Traits\CommanFunctions;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Settings;
use App\Models\taxi\Requests\RequestHistory;
use DB;
use Validator;
// use Kreait\Firebase\Database;

class ChangeLocationController extends BaseController
{
    use CommanFunctions;

    public $request;

    public function __construct(RequestModel $request) {
        
        $this->request = $request;
    }

    public function index(Request $request)
    {
        try{
            // DB::beginTransaction(); 
            $clientlogin = $this::getCurrentClient(request());
        
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);
            
            //Check the validation First
            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'address' => 'required',
                'type' => 'required', //PICKUP or DROP
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),422);       
            }

            $oldrequest = $this->request->where('id',$request->request_id)->with(['requestPlace','userDetail','driverDetail'])->first();

            if(is_null($oldrequest))
                return $this->sendError('Wrong Request', [],401);       

            // Check if the user has registred a trip already
            $this->validateUserInTripCancelled($user);

            if($request->type == 'PICKUP'){
                //Check whether the pickup must be in the range 
                $pickup_range_value = 1;
                $pickup_range = Settings::where('name','pick_up_location_change_distance_limit')->first();
                if(!is_null($pickup_range)){
                    $pickup_range_value = $pickup_range->value;
                }
               
                //find Distance 
                $new_pickup_distance = distanceBetweenTwoPoints($request->pick_lat, $request->pick_lng, $oldrequest->lattitude, $oldrequest->langitude);
                if($new_pickup_distance > (double)$pickup_range_value){
                    return $this->sendError('You can able to Change Location within '.$pickup_range_value, [],403);
                }

                $request_place_params = [
                    'pick_lat'     => $request->latitude,
                    'pick_lng'     => $request->longitude,
                    'pick_address' => $request->address,
                    'poly_string'  => $request->has('poly_string') && $request->poly_string != "" ? $request->poly_string : $oldrequest->requestPlace->poly_string
                ];
                $request_history_params = [
                    'olat'         => $request->latitude,
                    'olng'         => $request->longitude,
                    'pick_address' => $request->address,
                ];
                
                // $dd = new RequestHistory();
                // $dd->request_id = $oldrequest->id;
                // $dd->olat = $request->latitude;
                // $dd->olng =  $request->longitude;
                // $dd->pick_address = $request->address;
                // $dd->save();

      
                
                $oldrequest->requestPlace()->update($request_place_params);
                $oldrequest->requestHistory()->update($request_history_params);
                $requestHistory = RequestHistory::where('request_id',$oldrequest->id)->first();
            }else
            {
                $request_place_params = [
                    'drop_lat'     => $request->latitude,
                    'drop_lng'     => $request->longitude,
                    'drop_address' => $request->address
                ];
                $request_history_params = [
                    'dlat'         => $request->latitude,
                    'dlng'         => $request->longitude,
                    'drop_address' => $request->address
                ];
                
                $location_changed = ['location_approve' => 1];
                $oldrequest->update($location_changed);
                // $oldrequest->requestPlace()->update($request_place_params);
                $oldrequest->requestHistory()->create($request_history_params);
                $requestHistory = RequestHistory::where('request_id',$oldrequest->id)->where('olat',null)->first();
            }
           
           
            
            $title = Null;
            $body = '';
            $sub_title = Null;
            $lang = $user->language;
            $push_data = $this->pushlanguage($lang,'location-changed');
            if(is_null($push_data)){
                $title = 'Trip Location Changed 😊️';
                $body = 'Trip Location Changed Please Check it';
                $sub_title = 'Trip Location Changed Please Check it';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 

            $result = [
                'type'     => $request->type == 'PICKUP' ? 1 : 0,
                'latitude'     => $request->latitude,
                'longitude'     => $request->longitude,
                'address' => $request->address,
                'request_id' => $oldrequest->id,
                'location_id' => $requestHistory->id,
                'location_approve' => 1,
                'driver_accept'   => 0,
                'poly_string'     => $oldrequest->requestPlace ? $oldrequest->requestPlace->poly_string : ''
            ];

            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = 'trip_location_changed';
            $socket_data->result = $result;

            $socketData = ['event' => 'locationchanged_'.$oldrequest->driverDetail->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => "trip_location_changed", 'result' => $result];

            dispatch(new SendPushNotification($title,$sub_title,$pushData, $oldrequest->driverDetail->device_info_hash, $oldrequest->driverDetail->mobile_application_type,0));

           
        
            return $this->sendResponse('Data Found', $socket_data, 200);
        } catch (\Exception $e) {
            DB::rollback(); 
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function validateUserInTripCancelled($user)
    {
        
        $user_exists_trip = $this->request->where('is_cancelled', 0)->where('user_id', $user->id)->where('is_later', 0)->where('is_trip_start',1)->exists();

        if ($user_exists_trip) {
            return $this->sendError('User already in trip',[],400);
        }
    }


    public function validatePaymentOption($request)
    {
        switch ($request->payment_opt) {
            case "CARD": // Card payment
                return $this->checkcard($request);
                break;
            case "CASH": // Cash payment
                return true;
                break;
            case "WALLET": // Wallet payment
                return $this->checkwallet($request);
                break;
        }
    }

    public function checkCard()
    {
        // @TODO
    }

    /**
     * Check wallet exists or not 
     * 
    */
    public function checkWallet()
    {
        // @TODO
    }

    public function driverApprove(Request $request){
        $validator = Validator::make($request->all(), [
            'request_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' =>'required',
            'location_id' =>'required',
            'status' =>'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error',$validator->errors(),422);       
        }

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        $oldrequest = $this->request->where('id',$request->request_id)->first();
        if(is_null($oldrequest)){
            return $this->sendError('Wrong Request',[],403);
        }

        if($request->status == true){
            $location_changed = ['location_approve' => 0];
            $oldrequest->update($location_changed);

            $request_history_params = [
                'olat'         => $request->latitude,
                'olng'         => $request->longitude,
                'pick_address' => $request->address,
            ];
            
            // dd($request_history_params);
            $requestHistory = RequestHistory::where('id',$request->location_id)->where('olat',null)->first();
            // dd($requestHistory);
            if(is_null($requestHistory)){
                return $this->sendError('Wrong Request1',[],403);
            }
    
            $requestHistory->update($request_history_params);
            $request_place_params = [
                'drop_lat'     => $requestHistory->dlat,
                'drop_lng'     => $requestHistory->dlng,
                'drop_address' => $requestHistory->drop_address,
                'poly_string'  => $request->poly_string
            ];
    
           $oldrequest->requestPlace()->update($request_place_params);

            $result = [
                'location_type' => 0,
                'type'          => 0,
                'latitude'      =>  $requestHistory->dlat,
                'longitude'     => $requestHistory->dlng,
                'address'       => $requestHistory->drop_address,
                'request_id'    => $oldrequest->id,
                'location_id'   => $requestHistory->id,
                'driver_accept' => 1,
                'poly_string'   => $request->poly_string
            ];
            
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = 'trip_location_changed';
            $socket_data->result = $result;

            $socketData = ['event' => 'locationchanged_'.$oldrequest->userDetail->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => "trip_location_changed", 'result' => $result];

            dispatch(new SendPushNotification('trip_location_changed','trip_location_changed',$pushData, $oldrequest->userDetail->device_info_hash, $oldrequest->userDetail->mobile_application_type));

            return $this->sendResponse('Data Found', $socket_data, 200);

        }
        else{
            $location_changed = ['location_approve' => 0];
            $oldrequest->update($location_changed);

            $requestHistory = RequestHistory::where('id',$request->location_id)->where('olat',null)->first();
            if(is_null($requestHistory)){
                return $this->sendError('Wrong Request',[],403);
            }
            

            // $result = [
            //     'type'          => 0,
            //     'location_type' => 0,
            //     'latitude'     => $requestHistory->dlat,
            //     'longitude'     => $requestHistory->dlng,
            //     'address'       => $requestHistory->drop_address,
            //     'request_id'    => $oldrequest->id,
            //     'location_id'   => $requestHistory->id,
            //     'driver_accept'   => 0,
            // ];

            $result = [
                'type'          => 0,
                'location_type' => 0,
                'latitude'      => $oldrequest->pick_lat,
                'longitude'     => $oldrequest->pick_lng,
                'address'       => $oldrequest->drop_address,
                'request_id'    => $oldrequest->id,
                'location_id'   => $requestHistory->id,
                'driver_accept'   => 0,
            ];
            
            $requestHistory->delete();
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = 'drive_not_approved';
            $socket_data->result = $result;

            $socketData = ['event' => 'locationchanged_'.$oldrequest->userDetail->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => "drive_not_approved", 'result' => $result];

            dispatch(new SendPushNotification('drive_not_approved','drive_not_approved',$pushData, $oldrequest->userDetail->device_info_hash, $oldrequest->userDetail->mobile_application_type));

            return $this->sendResponse('Data Found', $socket_data, 200);
        }        
    }


    public function fetchDiverDistance(){
        $drivers = fetchDriverDistance();
        return $drivers;
    }
}
