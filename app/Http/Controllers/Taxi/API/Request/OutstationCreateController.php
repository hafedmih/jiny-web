<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Http\Controllers\Controller;
use App\Constants\PushEnum;
use App\Http\Requests\Taxi\API\Request\CreateTripRequest;
use App\Traits\CommanFunctions;
use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Transformers\Request\TripRequestTransformer;
use DB;
use App\Models\taxi\Vehicle;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Traits\RandomHelper;
use App\Models\taxi\Promocode;
use App\Http\Controllers\API\BaseController as BaseController;

class OutstationCreateController extends BaseController
{
    use CommanFunctions,RandomHelper;
    
    public function rideOutstation($request,$user)
    {
        $requestNumber = generateRequestNumber();

        if(!$request->has('outstation_id')){
            return $this->sendError('Sorry please select drop',[],403);
        }
    
        $outstationMaster = OutstationMaster::where('id',$request->outstation_id)->first();
        if(is_null($outstationMaster)){
            return $this->sendError('Wrong Drop',[],403);
        }

        $type = Vehicle::where('slug',$request->vehicle_type)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }

        $outstationPricing = OutstationPriceFixing::where('type_id',$type->id)->first();
        if(is_null($outstationPricing)){
            return $this->sendError('Wrong Drop',[],403);
        }

        $promocode_id =0;
        if (request()->has('promo_code') && $request->promo_code != ""){
            
            $promocode = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
            if(is_null($promocode))
                return $this->sendError('Wrong Promo Code',[],403);
                
            $promocode_id = $promocode->id;

            $promo_count = RequestModel::where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
            if($promo_count > $promocode->promo_user_reuse_count)
                return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);

            $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count > $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);

            if(!in_array($type->id,$promocode->types))
                return $this->sendError('Sorry! promo code exit',[],403);
        }

        if($request->has('is_later') && $request->is_later == "1"){
            $trip_start_time = $request->trip_start_time;
            $is_later = 1;
            $ride_type = 'Ride Later';
        }
        else{
            $trip_start_time = NOW();
            $is_later = 0;
            $ride_type = 'Ride Now';
        }
        $request_otp = $this->UniqueRandomNumbers(4);
        $request_params = [
           
            'is_later'                => $is_later,
            'trip_start_time'         => $trip_start_time,
            'request_number'          => $requestNumber,
            'request_otp'             => $request_otp, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'payment_opt'             => $request->payment_opt,
            'outstation_id'           => $outstationMaster->id,
            'outstation_type_id'      => $outstationPricing->id,
            'promo_id'                => $promocode_id,
            'requested_currency_code' => $outstationMaster->getCountry->currency_code,
            'requested_currency_symbol' => $outstationMaster->getCountry->currency_symbol,
            // 'driver_info'             => $request->driver_notes,
            'ride_type'               => $ride_type,
            'trip_type'               => $request->ride_type,
            'outstation_trip_type'    => $request->trip_way_type,
            'trip_end_time'           => $request->trip_end_time

        ];
        // dd($request_params);
        // dd("hai");
        $request_detail = RequestModel::create($request_params);

        // request place detail params
        $request_place_params = [
            'pick_lat'     => $request->pick_lat,
            'pick_lng'     => $request->pick_lng,
            'drop_lat'     => $request->drop_lat,
            'drop_lng'     => $request->drop_lng,
            'pick_address' => $request->pick_address,
            'drop_address' => $request->drop_address,
            // 'poly_string'  => $request->poly_string,
        ];

        $request_history_params = [
            'olat'         => $request->pick_lat,
            'olng'         => $request->pick_lng,
            'dlat'         => $request->drop_lat,
            'dlng'         => $request->drop_lng,
            'pick_address' => $request->pick_address,
            'drop_address' => $request->drop_address
        ];
        
        $request_detail->requestHistory()->create($request_history_params);
        $request_detail->requestPlace()->create($request_place_params);
        
        // $result = fractal($request_detail, new TripRequestTransformer);
        $request_result =  fractal($request_detail, new TripRequestTransformer);
        if(!$request->is_later){
            $selected_drivers = [];

            $drivers = fetchDrivers($request->pick_lat,$request->pick_lng,$request->vehicle_type, $request->ride_type);
            $drivers = json_decode($drivers->getContent());

            // dd($drivers);
            if ($drivers->success == true) {
                foreach ($drivers->data as $key => $driver) {
                    $driverdet = User::where('slug',$driver->id)->first();
                    if($driverdet){
                        // $metta = RequestMeta::where('driver_id',$driverdet->id )->count();
                        $metta = RequestMeta::where('driver_id',($driverdet->id == null)?' ':$driverdet->id )->count();
                        if($driverdet->active && $metta == 0){
                            $selected_drivers[$key]["user_id"] = $user->id;
                            $selected_drivers[$key]["driver_id"] = $driverdet->id;
                            $selected_drivers[$key]["active"] = ($key == 0 ? 1 : 0);
                            $selected_drivers[$key]["request_id"] = $request_detail->id;
                            $selected_drivers[$key]["assign_method"] = 1;
                            $selected_drivers[$key]["created_at"] = date('Y-m-d H:i:s');
                            $selected_drivers[$key]["updated_at"] = date('Y-m-d H:i:s');
                        }
                    }
                }
            }else{
                return $this->sendError('No Driver Found',[],404);  
            }
            if(count($selected_drivers) == 0){
                return $this->sendError('No Driver Found',[],404);  
            }

            // $metaDriverslug = $selected_drivers[0]['driver_id'];

            $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
        
            $result = fractal($request_detail, new TripRequestTransformer);
            // $result['request_number'] = $request_detail->request_number;

            $title = Null;
            $body = '';
            $lang = $metaDriver->language;

            $push_data = $this->pushlanguage($lang,'trip-created');
            if(is_null($push_data)){
                $title = 'New Trip Requested 😊️';
                $body = 'New Trip Requested, you can accept or Reject the request';
                $sub_title = 'New Trip Requested, you can accept or Reject the request';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            }   


            $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
            // dd($pushData);
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CREATED;
            $socket_data->result = $result;

             $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
             sendSocketData($socketData);

            // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];

            // dd($metaDriver->mobile_application_type);
            dispatch(new SendPushNotification($title,$sub_title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));

            // dd($selected_drivers);
            foreach ($selected_drivers as $key => $selected_driver) {
                $request_meta = $request_detail->requestMeta()->create($selected_driver);
            
            }
        }

        DB::commit();
        return $this->sendResponse('Data Found', $request_result, 200);
    }


}
