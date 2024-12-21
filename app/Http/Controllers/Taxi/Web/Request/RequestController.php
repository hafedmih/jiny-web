<?php

namespace App\Http\Controllers\Taxi\Web\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Transformers\Request\TripRequestTransformer;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestHistory;
use App\Models\taxi\Promocode;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Driver;
use App\Models\taxi\Requests\RequestBill;
use App\Models\User;
use App\Models\taxi\Settings;
use App\Models\taxi\CancellationRequest;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\Requests\NoDriverTrips;

use App\Constants\PushEnum;
use App\Constants\RideType;
use App\Constants\AdminCommissionType;
use App\Jobs\SendPushNotification;
use Carbon\Carbon;
use App\Traits\CommanFunctions;

class RequestController extends BaseController
{
    use CommanFunctions;
    public function request(Request $request)
    {
    	// $requests_now = RequestModel::orderBy('created_at','desc')->where('is_later',0)->where('trip_type','LOCAL')->get();
        $requests_now = RequestModel::orderBy('created_at','desc')->where('is_later',0)->where('trip_type','LOCAL');
        $result=[
            'requests_now' => $requests_now->paginate(10),
            'requests_later' =>[],
            'requests_rental_now' => [],
            'requests_rental_later' => [],
            'outstation_list' => [],
            'cancelled_trips' => [],
            'on_going_trips' => [],
        ];
        
        return view('taxi.requests.Request',['result' => $result]);

        // return view('taxi.requests.Request', compact('requests_now','requests_later'));
    }

    public function requests_later(Request $request)
    {
    	$requests_later = RequestModel::orderBy('created_at','desc')->where('is_later',1)->where('trip_type','LOCAL');

        $result=[
            'requests_now' => [],
            'requests_later' =>$requests_later->paginate(10),
            'requests_rental_now' => [],
            'requests_rental_later' => [],
            'outstation_list' => [],
            'cancelled_trips' => [],
            'on_going_trips' => [],
        ];
        return view('taxi.requests.Request',['result' => $result]);

    }
    public function requests_rental_now(Request $request)
    {
    	 $requests_rental_now = RequestModel::where('is_later',0)->where('trip_type','RENTAL')->orderby('created_at','desc');

        $result=[
            'requests_now' => [],
            'requests_later' =>[],
            'requests_rental_now' => $requests_rental_now->paginate(10),
            'requests_rental_later' => [],
            'outstation_list' => [],
            'cancelled_trips' => [],
            'on_going_trips' => [],
        ];


          return view('taxi.requests.Request',['result' => $result]);

    }
    public function requests_rental_later(Request $request)
    {
    	$requests_rental_later = RequestModel::where('is_later',1)->where('trip_type','RENTAL')->orderby('created_at','desc');
        
        $result=[
            'requests_now' => [],
            'requests_later' =>[],
            'requests_rental_now' => [],
            'requests_rental_later' => $requests_rental_later->paginate(10),
            'outstation_list' => [],
            'cancelled_trips' => [],
            'on_going_trips' => [],
        ];

                 return view('taxi.requests.Request',['result' => $result]);

    }
    public function outstation_list(Request $request)
    {
    	 $outstation_list = RequestModel::where('trip_type','OUTSTATION')->orderby('created_at','desc');
         
        $result=[
            'requests_now' => [],
            'requests_later' =>[],
            'requests_rental_now' => [],
            'requests_rental_later' => [],
            'outstation_list' => $outstation_list->paginate(10),
            'cancelled_trips' => [],
            'on_going_trips' => [],
        ];



         return view('taxi.requests.Request',['result' => $result]);

    }
    public function cancelled_trips(Request $request)
    {
    	$cancelled_trips = RequestModel::where('is_cancelled',1)->orderby('created_at','desc');
        
        $result=[
            'requests_now' => [],
            'requests_later' =>[],
            'requests_rental_now' => [],
            'requests_rental_later' => [],
            'outstation_list' => [],
            'cancelled_trips' => $cancelled_trips->paginate(10),
            'on_going_trips' => [],
        ];


         return view('taxi.requests.Request',['result' => $result]);

    }
    public function on_going_trips(Request $request)
    {
        $on_going_trips = RequestModel::where('is_trip_start',1)->where('is_completed',0)->where('is_cancelled',0)->orderby('created_at','desc');

        $result=[
            'requests_now' => [],
            'requests_later' =>[],
            'requests_rental_now' => [],
            'requests_rental_later' => [],
            'outstation_list' => [],
            'cancelled_trips' => [],
            'on_going_trips' => $on_going_trips->paginate(10),
        ];


                 return view('taxi.requests.Request',['result' => $result]);

    }
    public function requestView($id)
    {
    	$requests = RequestModel::where('id',$id)->first();
        
        $outstation_trip = OutstationUploadImages::where('request_id',$requests->id)->first();
        
    	return view('taxi.requests.RequestView', ['request' => $requests ,'outstation_trip' => $outstation_trip]);
    }

    public function requestViews($id)
    {
    	$requests = RequestModel::where('id',$id)->with('getZonePrice')->first();

        $type = $requests->getZonePrice->type_id;

        $packages = PackageItem::where('type_id',$type)->with('getPackage','getPackage.getCountry')->get();
        
    	return response()->json(['success' => true, 'request' => $requests, 'packages' => $packages]);
    }

    public function requestEnd($ride)
    {
        $request_detail = RequestModel::where('id',$ride)->first();
        // Validate Trip request data
        // if ($request_detail->is_completed) {
        //     // @TODO send success response with bill object
        //     $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');

        //     return $this->sendResponse('Data Found', $request_result, 200);
        // }
     

        // if ($request_detail->is_cancelled) {
        //     return $this->sendError('Request Cancelled',[],401);
        // }
        // Update Driver state as Available
        $request_detail->driverDetail->driver->update(['available'=>true]);

    //    $distance = (double)$request->distance;

        $request_history = RequestHistory::where('olat','!=',Null)->where('olng','!=',Null)->where('dlat','!=',Null)->where('dlng','!=',Null)->where('request_id',$request_detail->id)->get();
        $distance  = 0;
        if(!is_null($request_history)){
            foreach($request_history as $reques_data){
                // dd($reques_data);
                $newdistance = $this->getDistance($reques_data->olat,$reques_data->olng,$reques_data->dlat,$reques_data->dlng);
                $distance = $distance+$newdistance;
            }
        }

        // dd($distance);
         
        $duration = $this->calculateDurationOfTrip($request_detail->trip_start_time);
        $waitingTime = $request->waiting_time;
        $requestParams = [
            'is_completed'   => 1,
            'completed_at'   => now(),
            'total_distance' => $distance,
            'total_time'     => $duration,
            'is_paid'        => 1
        ];

       $request_detail->update($requestParams);  

        $promo_detail =null;
        if ($request_detail->promo_id) {
            $promo_detail = $this->validateAndGetPromoDetail($request_detail);
        }

     //   dd($promo_detail);
        $calculated_bill =  $this->calculateRideFares($distance, $duration, $waitingTime, $request_detail, $promo_detail);
      
        // @TODO need to take admin commision from driver wallet
        // if ($request->payment_opt) {
        //     # code...
        // }

        $calculated_bill['request_id'] = $request_detail->id;
        $calculated_bill['requested_currency_code'] = $request_detail->requested_currency_code;
        $calculated_bill['requested_currency_symbol'] = $request_detail->requested_currency_symbol;
        $request_bill = RequestBill::create($calculated_bill);
        
        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
        
        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);

            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'trip-end');
            if(is_null($push_data)){
                $title = 'Driver Ended the trip';
                $body = 'Driver finished the ride, Please help us by rate the driver';
                $sub_title = 'Driver finished the ride, Please help us by rate the driver';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 


           
            $push_data = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP, 'result'=>(string)$push_request_detail];

            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::DRIVER_END_THE_TRIP;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);
    
            // $pushData = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP, 'result' => (string) $request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP, 'result' => $request_result];
            dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));
        }

        $user_refernce_amount = Settings::where('name','wallet_driver_refernce_amount')->first();
        $referan_amount_trip_count = Settings::where('name','referan_amount_trip_count')->first();
        $referan_amount_trip_count = $referan_amount_trip_count ? $referan_amount_trip_count->value : 0;
        $refer_user = User::where('id',$request_detail->driver_id)->first();
        $driver = Driver::where('user_id',$refer_user->id)->first();

        if($refer_user){
            $request_count = RequestModel::where('driver_id',$refer_user->id)->where('is_completed',1)->count();

            if($request_count >= $referan_amount_trip_count && $driver->refernce_count > 0){
                $wallet = Wallet::where('user_id',$refer_user->id)->first();
                if($wallet){
                    $wallet->earned_amount += $user_refernce_amount ? $user_refernce_amount->value * $driver->refernce_count : 0;
                    $wallet->balance_amount += $user_refernce_amount ? $user_refernce_amount->value * $driver->refernce_count : 0;
                }
                else{
                    $wallet = Wallet::create([
                        'user_id' => $refer_user->id,
                        'earned_amount' => $user_refernce_amount ? $user_refernce_amount->value * $driver->refernce_count : 0,
                        'balance_amount' => $user_refernce_amount ? $user_refernce_amount->value * $driver->refernce_count : 0,
                        'amount_spent' => 0
                    ]);
                }
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $user_refernce_amount ? $user_refernce_amount->value * $driver->refernce_count : 0,
                    'purpose' => "Refernce Amount",
                    'type' => "EARNED",
                    'user_id' => $refer_user->id
                ]);
                Driver::where('user_id',$refer_user->id)->update(['refernce_count' => 0]);
            }
        }
        
        return $this->sendResponse('Data Found', $request_result, 200);
    }

    public function calculateDurationOfTrip($start_time)
    {
        $start_time = Carbon::parse($start_time);
        
        $total_duration = now()->diffInMinutes($start_time);

        return $total_duration;
    }

    public function validateAndGetPromoDetail($request_detail)
    {
        $promo_code_id = $request_detail->promo_id;
        $user_id = $request_detail->user_id;

        $promo = Promocode::whereStatus(true)->where('id', $promo_code_id)->first();

        if (!$promo) {
            return null;
        }

        return $promo;
    }

    public function calculateRideFares($distance, $duration, $waitingTime, $request_detail, $promo_detail)
    {
        $price = ZonePrice::whereId($request_detail->zone_type_id)->first();
       
        if ($request_detail->ride_type == RideType::RIDENOW) {
            $basePrice     = $price->ridenow_base_price;
            $distancePrice = $price->ridenow_price_per_distance;
            $timePrice     = $price->ridenow_price_per_time;
            $baseDistance  = (double)$price->ridenow_base_distance;
            $freeWaiting   = $price->ridenow_free_waiting_time;
            $waitingCharge = $price->ridenow_waiting_charge;
        } else {
            $basePrice     = $price->ridelater_base_price;
            $distancePrice = $price->ridelater_price_per_distance;
            $timePrice     = $price->ridelater_price_per_time;
            $baseDistance  = (double)$price->ridelater_base_distance;
            $freeWaiting   = $price->ridelater_free_waiting_time;
            $waitingCharge = $price->ridelater_waiting_charge;
        }

        // Surge price
        $today = now()->dayName;
        foreach ($price->getSurgePrice as $key => $surge) {
            $startDate = now()->parse($surge->start_time);
            $endDate = now()->parse($surge->end_time);

            if(now()->gte($startDate) && now()->lte($endDate) && $surge->available_days){
                $exploded_days=explode(',',$surge->available_days);

                if(in_array($today,$exploded_days)){
                    $basePrice = $surge->surge_price;
                    break;
                }
            }
        }
        
      
        if($distance == 0.00){
            $distance = 0.00;
        }elseif($distance < $baseDistance){
            $distance = 0.00;
        }else{
            $distance = $distance - $baseDistance;
        }
        $totalTripTime = 0;
        if($duration != 0){
            if($duration < $waitingTime){
                $totalTripTime = $duration - $waitingTime;
            }
            else{
                $totalTripTime = 0;
            }
        }
        
       
        $totalWaitingTime = $waitingTime;
      
        $finalWaitingPrice = $totalWaitingTime  * $waitingCharge;
     
        $totalDistancePrice = $distance * $distancePrice;
        
        $totalTimePrice = $totalTripTime * $timePrice;
        
        $subTotal = $basePrice + $totalTimePrice + $totalDistancePrice + $finalWaitingPrice;
      
        $adminCommission = $price->getZone->admin_commission;

        $adminCommissionType = $price->getZone->admin_commission_type;
        
        if ($adminCommissionType == AdminCommissionType::PERCENTAGE) {
            $adminServiceFee = ($subTotal * ($adminCommission / 100));
           
        } else {
            $adminServiceFee = $adminCommission;
        }
        
        $driver_details = Driver::where('user_id',$request_detail->driver_id)->first();

        if($driver_details && $driver_details->subscription_type == 'SUBSCRIPTION'){
            $adminServiceFee = 0;
        }
     
        //  $subTotal = $subTotal + $adminServiceFee;
        // dd($subTotal);
//  dd($subTotal);
        $discount_amount = 0;
        // Promo Discount
        $amount_without_promo = $subTotal;
        
        if (!is_null($promo_detail)) {
            
            if($promo_detail->promo_type == 1){
                $discount_amount = $promo_detail->amount;
            }else if($promo_detail->promo_type == 2){
                $discount_amount = ($promo_detail->percentage / 100) * $subTotal;
            }
          
            if ($discount_amount < $subTotal) {
            //    echo $subTotal."---";
                $subTotal = $subTotal - $discount_amount;
                // dd($subTotal);
            }else{
                $subTotal = 0;
            }
        }

        $driverCommission = ($subTotal - $adminServiceFee) > 0 ? $subTotal - $adminServiceFee : 0;
        $total = $subTotal;
    //    dd($discount_amount);

        return [
            'base_price'                => $basePrice,
            'base_distance'             => $basePrice,
            'price_per_distance'        => $distancePrice,
            'distance_price'            => $totalDistancePrice,
            'price_per_time'            => $timePrice,
            'time_price'                => $totalTimePrice,
            'promo_discount'            => $discount_amount,
            'waiting_charge'            => $finalWaitingPrice,
            'admin_commision'           => $adminServiceFee,
            'driver_commision'          => $driverCommission,
            'total_amount'              => $total,
            'sub_total'                 => $subTotal,
            'total_distance'            => $distance,
            'total_time'                => $totalTripTime,
            'requested_currency_code'   => $request_detail->requested_currency_code,
            'requested_currency_symbol' => $request_detail->requested_currency_symbol
        ];
    }

    public function CancelRequest(Request $request)
    {
        $requests = CancellationRequest::orderBy('id', 'DESC')->get();
    	return view('taxi.requests.index', compact('requests'));
    }

    public function CancelDeleteRequest(Request $request)
    {
        $requests = NoDriverTrips::orderBy('id', 'DESC')->get();
    	return view('taxi.requests.NoDriverTrips', compact('requests'));
    }

    public function requestCategoryChange(Request $request)
    {
        $requests = RequestModel::where('id', $request->request_id)->where('is_completed',0)->where('is_cancelled',0)->first();

        if($requests && $requests->trip_type == "LOCAL" && $request->has('manual_trip') && $request->manual_trip == "YES" && $request->has('package_id') && $request->package_id != ""){
            $package = PackageItem::where('id',$request->package_id)->first();
            $requests->trip_type = "RENTAL";
            $requests->package_item_id = $request->package_id;
            $requests->package_id = $package->package_id;
            $requests->save();

            if(date('Y-m-d H:i:s') >= date('Y-m-d H:i:s',strtotime($requests->trip_start_time))){
                $request_result =  fractal($requests, new TripRequestTransformer);
                $userModel = User::find($requests->user_id);
                
                $title = Null;
                $body = '';
                $lang = $userModel->language;
                $push_data = $this->pushlanguage($lang,'local-to-rental');
                if(is_null($push_data)){
                    $title = 'Local to Rental Category';
                    $body = 'Local to Rental Category';
                    $sub_title = 'Local to Rental Category';

                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                } 

                $pushData = ['notification_enum'=>PushEnum::LOCAL_TO_RENTAL];
                
                // $push_data = ['notification_enum'=>PushEnum::LOCAL_TO_RENTAL,'result'=>(string)$push_request_detail];
                // dd($push_data);
                // Form a socket sturcture using users'id and message with event name
                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = PushEnum::LOCAL_TO_RENTAL;
                $socket_data->result = $request_result;

                $socketData = ['event' => 'package_changed_'.$userModel->slug,'message' => $socket_data];
                sendSocketData($socketData);

                dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));

                $driverModel = User::find($requests->driver_id);
                if($driverModel){
                    $title = Null;
                    $body = '';
                    $lang = $driverModel->language;
                    $push_data = $this->pushlanguage($lang,'local-to-rental');
                    if(is_null($push_data)){
                        $title = 'Local to Rental Category';
                        $body = 'Local to Rental Category';
                        $sub_title = 'Local to Rental Category';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    } 

                    $pushData = ['notification_enum'=>PushEnum::LOCAL_TO_RENTAL];
                    
                    // $push_data = ['notification_enum'=>PushEnum::LOCAL_TO_RENTAL,'result'=>(string)$push_request_detail];
                    // dd($push_data);
                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::LOCAL_TO_RENTAL;
                    $socket_data->result = $request_result;

                    $socketData = ['event' => 'package_changed_'.$driverModel->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    dispatch(new SendPushNotification($title, $pushData, $driverModel->device_info_hash, $driverModel->mobile_application_type,0,$sub_title));
                }
            }

            return $this->sendResponse('Category Updated Successfully', $requests, 200);
        }
        return $this->sendError('Invalide Trip', $requests, 404);
    }

    
}
