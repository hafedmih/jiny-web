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
use App\Http\Controllers\API\BaseController as BaseController;

class CreateRideLaterController extends BaseController
{
    use CommanFunctions;
    
    public function rideLater(CreateTripRequest $request,$zone,$user)
    {
        $requestNumber = generateRequestNumber();

        $type = Vehicle::where('slug',$request->vehicle_type)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }

        // check whether User already have trips 
        $userAlreadyTrip = RequestModel::where('user_id',$user->id)->where('is_later',1)->where('is_completed','!=',1)->where('is_cancelled','!=',1)->first();
        if(!is_null($userAlreadyTrip))
            return $this->sendError('You allowed to run only one Ride later ',[],403);

        $zone = $this->getZone($request->pick_lat, $request->pick_lng);
        // dd($type->id);
        $zone_type_id = 0;
        foreach($zone->getZonePrice as $zoneprice){
           
            if($zoneprice->type_id == $type->id){
                $zone_type_id = $zoneprice->id;
            }
        }
        $promocode_id =0;
            if (request()->has('promo_code') && $request->promo_code != ""){
                
                $promocode = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
                if(is_null($promocode))
                    return $this->sendError('Wrong Promo Code',[],403);
                    
                $promocode_id = $promocode->id;

                $promo_count = RequestModel::where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();

                if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count)
                    return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],403);
                if($promo_count >= $promocode->promo_user_reuse_count)
                    return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);

                $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
                if($promo_all_count >= $promocode->promo_use_count)
                    return $this->sendError('Sorry! promo code exit',[],403);

                if(!in_array($type->id,$promocode->types))
                    return $this->sendError('Sorry! promo code exit',[],403);
            }

        $request_params = [
           
            'is_later'                => true,
            'trip_start_time'         => $request->trip_start_time,
            'request_number'          => $requestNumber,
            'request_otp'             => 1234, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'zone_type_id'            => $zone_type_id,
            'payment_opt'             => $request->payment_opt,
            'promo_id'                => $promocode_id,
            'unit'                    => $zone->unit,
            'requested_currency_code' => $zone->getCountry->currency_code,
            'requested_currency_symbol' => $zone->getCountry->currency_symbol,
            'driver_info'             => $request->driver_notes,
            'ride_type'               =>  "Ride Later",
            'trip_type'               => $request->ride_type

        ];
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
            'poly_string'  => $request->poly_string,
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
        
        $result = fractal($request_detail, new TripRequestTransformer);
        DB::commit();
        return $this->sendResponse('Data Found', $result, 200);
    }


}
