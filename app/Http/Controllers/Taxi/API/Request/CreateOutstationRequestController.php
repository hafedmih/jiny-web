<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Taxi\API\Request\CreateTripRequest;
use App\Traits\CommanFunctions;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Transformers\Request\TripRequestTransformer;
use DB;
use App\Models\taxi\Vehicle;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Http\Controllers\API\BaseController as BaseController;

class CreateOutstationRequestController extends Controller
{
    public function rideOutstation($request,$user)
    {
        $requestNumber = generateRequestNumber();

        $userAlreadyTrip = RequestModel::where('user_id',$user->id)->where('is_later',1)->where('is_completed','!=',1)->where('is_cancelled','!=',1)->first();
        if(!is_null($userAlreadyTrip))
            return $this->sendError('You allowed to run only one Ride later ',[],403);

        $type = Vehicle::where('slug',$request->vehicle_type)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }

        $outstation = OutstationMaster::where('id',$request->outstation_id)->first();
        if(is_null($outstation)){
            return $this->sendError('wrong Outstation place',[],403);
        }

        $outstation_type = OutstationPriceFixing::where('type_id',$type->id)->first();

 //dd("dai");
        $request_params = [
           
            'is_later'                => true,
            'trip_start_time'         => $request->trip_start_time,
            'trip_end_time'         => $request->trip_end_time,
            'request_number'          => $requestNumber,
            'request_otp'             => 1234, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'payment_opt'             => $request->payment_opt,
            'requested_currency_code' => $outstation->getCountry->currency_code,
            'requested_currency_symbol' => $outstation->getCountry->currency_symbol,
            'ride_type'               =>  "Ride Later",
            'trip_type'               => $request->ride_type,
            'manual_trip'             => 'AUTOMATIC',
            'outstation_id'           => $outstation->id,
            'outstation_type_id'      => $outstation_type->id

            
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
        
        $result = fractal($request_detail, new TripRequestTransformer);
        DB::commit();
        return $this->sendResponse('Data Found', $result, 200);
    }
}
