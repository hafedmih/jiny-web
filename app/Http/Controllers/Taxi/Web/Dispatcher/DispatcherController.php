<?php

namespace App\Http\Controllers\Taxi\Web\Dispatcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\CancelMethod;
use App\Traits\CommanFunctions;
use App\Http\Controllers\Taxi\Web\Dispatcher\DispatcherRideLaterController;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Transformers\Request\TripRequestTransformer;
use App\Constants\PushEnum;
use App\Jobs\SendPushNotification;
use App\Models\boilerplate\OauthClients;
use App\Traits\RandomHelper;
use Illuminate\Support\Facades\Http;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Customer;
use App\Models\taxi\Promocode;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Driver;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\Outofzone;
use App\Models\taxi\Zone;
use App\Models\User;
use App\Models\taxi\OutstationUploadImages;
use Illuminate\Support\Carbon;

class DispatcherController extends BaseController
{
    use CommanFunctions,RandomHelper;

    public $request;

    public function __construct(RequestModel $request) {
        
        $this->request = $request;
    } 

    public function dispatcher(Request $request)
    {
        $zone = Zone::where('zone_name','Nouakchott')->where('status',1)->first();
        $package_detail = PackageMaster::where('status',1)->get();

        $outstanding_pickup = OutstationMaster::where('status',1)->groupby('pick_up')->get();
        $outstanding_drops = OutstationMaster::where('status',1)->groupby('drop')->get();
        $outstation_price = OutstationPriceFixing::where('status',1)->get();

        return view('taxi.dispatcher.DispatcherAddTrip',['package_detail' => $package_detail,'outstanding_pickup' => $outstanding_pickup,'outstanding_drops' => $outstanding_drops,'outstation_price' => $outstation_price,'zone' => $zone]);
    }

    public function getCustomer($number)
    {
    	$customers = User::where('phone_number','LIKE','%'.$number.'%')->role('user')->get();

    	if(is_null($customers)){
    		$response = [
	            'success' => false,
	            'data'    => [],
	            'message' => "Data Not Found",
	        ];
            return response()->json($response, 200);
        }
        else{
           	$response = [
	            'success' => true,
	            'data'    => $customers,
	            'message' => "Data Found",
	        ];
            return response()->json($response, 200);
            return $this->sendResponse('Data Found',$response,200);  
        }
    }

    public function getVehicles(Request $request)
    {
        $data = $request->all();

        // dd("hai");
        
        $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);

       

        if(is_null($zone))
            return $this->sendError('Non services zone',[],404);

        // if($zone->non_service_zone == 'Yes'){
        //     return $this->sendError('Non services zone',[],404);
        // }
        // get distance for pickup lat long to drop lat long
        $distance = 0;
        if ($request->has('stops')) {
            $stops = json_decode($request->stops);
            for($i=0;$i<count($stops);$i++){
                if($i == 0){

                    $distance = $distance + $this->getDistance($data['pickup_lat'],$data['pickup_long'],$stops[$i]->latitude,$stops[$i]->longitude);

                }elseif($i == count($stops)){

                    $distance = $distance + $this->getDistance($stops[$i-1]->latitude,$stops[$i-1]->longitude,$data['drop_lat'],$data['drop_long']);

                }else{
                    
                    $distance = $distance + $this->getDistance($stops[$i-1]->latitude,$stops[$i-1]->longitude,$stops[$i]->latitude,$stops[$i]->longitude);
                }
                if($i == (count($stops)-1)){

                    $distance = $distance + $this->getDistance($stops[$i]->latitude,$stops[$i]->longitude,$data['drop_lat'],$data['drop_long']);

                }
            
            }
        }else{
            if($data['drop_address'] != ''){
                $distance = $this->getDistance($data['pickup_lat'],$data['pickup_long'],$data['drop_lat'],$data['drop_long']);
            }
        }

        // get eta calculation
        $datas = (object) [
            'zone_name' => $zone->zone_name,
            'country_name' => $zone->getCountry->name,
            'currency_symble' => $zone->getCountry->currency_symbol,
            'zone_slug' => $zone->slug,
            'payment_types' => explode(',',$zone->payment_types),
            'unit' => $zone->unit,
            'country_id' => $zone->country
        ];

            $zone_price = [];
            $data['ride_time'] = $data['ride_time'] ? $data['ride_time'] : NOW();
            foreach ($zone->getZonePrice as $key => $value) {
                $zonePrice = (object) [
                    'type_name' => $value->getType->vehicle_name,
                    'type_slug' => $value->getType->slug,
                    'capacity' => $value->getType->capacity,
                    'vehicle_id' => $value->getType->id,
                    'category' => $value->getType->getCategory ? $value->getType->getCategory->category_name : '',
                    'type_image' => $value->getType->image,
                ];

               $drop_zone = $this->getZone($data['drop_lat'],$data['drop_long']);
               
                $outofzonefee = 0;
                if(!$drop_zone){                  
                    $int_distance = (int)$distance;
                    // dd($int_distance);
                    $outofzone = Outofzone::orderby('id','desc')->get();
                    // dd($outofzone);
                    foreach($outofzone as $out){
                        
                        if($out->kilometer >= $int_distance){
                            // echo $out->price;
                            $outofzonefee = $out->price;
                        }
                        else{
                            $outofzone1 = Outofzone::orderby('id','desc')->first();
                            $outofzonefee = $outofzone1->price;
                        }
                    }
                    // dd($out_of_zone_price);
                }else{
                    if($drop_zone->non_service_zone == 'Yes'){
                        $int_distance = (int)$distance;
                        $outofzone = Outofzone::orderby('id','desc')->get();

                        foreach($outofzone as $out){
                            // dd($out->kilometer);
                            if((int)$out->kilometer >= $int_distance){
                                
                                $outofzonefee = $out->price;
                            }
                            else{
                                $outofzone1 = Outofzone::orderby('id','desc')->first();
                                $outofzonefee = $outofzone1->price;
                            }
                        }
                    }
                }
                $totalvalue = [];

                // if ( $data['trip_type'] == 'local') {
                //     $getZone =  $this->getZone($data['drop_lat'],$data['drop_long']);
        
                //     if($getZone == null)
                //     {
                //         return $this->sendError('Please choose outstation trip',[],404);
                        
                //     }
                // } 

                
               //Ride Now
             
                if($data['ride_type'] == "RIDE_NOW"){

                    $totalvalue = $this->etaCalculation($distance,$value->ridenow_base_distance,$value->ridenow_base_price,$value->ridenow_price_per_distance,$value->ridenow_booking_base_fare,$value->ridenow_booking_base_per_kilometer,$outofzonefee);
                }
                // Ride Later
                else if($data['ride_type'] == "RIDE_LATER"){

                    $totalvalue = $this->etaCalculation($distance,$value->ridelater_base_distance,$value->ridelater_base_price,$value->ridelater_price_per_distance,$value->ridelater_booking_base_fare,$value->ridelater_booking_base_per_kilometer,$outofzonefee);
                }
                
                $total_amount = $totalvalue['sub_total'];

                $zonePrice->promo_total_amount = $total_amount;
                $zonePrice->promo_msg = "";
                // dd($total_amount);
                if (request()->has('promo_code') && $request['promo_code'] != "") {
                    $expired = Promocode::whereStatus(true)->where('promo_code', $request['promo_code'])->first();
                    // dd($expired);
                    if(!$expired || $expired->select_offer_option == 4 && $expired->from_date > date('Y-m-d') || $expired->select_offer_option == 4 && $expired->to_date < date('Y-m-d') ){
                        return $this->sendError('Invalid Prome code',[],404);
                    }
                    // $promo_count = $this->request->where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
                    // if($expired->select_offer_option == 1 && $promo_count >= $expired->new_user_count){
                    //     return $this->sendError('Invalid Prome code',[],404);
                    // }
                    // if($expired->select_offer_option == 5 && $user->id != $expired->user_id){
                    //     return $this->sendError('Invalid Prome code',[],404);
                    // }
                    if ($expired->promo_code == $request['promo_code']) {    
                        if(in_array($value->getType->id,$expired->types)){
                            // dump($value->getType->vehicle_name);
                            // dump($total_amount,$expired->target_amount);
                            // dump($total_amount > $expired->target_amount);
                            if($total_amount > $expired->target_amount){
                                $zonePrice->promo_code = 1;

                                $total_amounts = $this->promoCalculation($expired,$total_amount);

                                $zonePrice->promo_total_amount = $total_amounts;
                                $total_amounts = str_replace(',', '', $total_amounts);
                                $amounts = (double) $total_amount - (double) $total_amounts;
                                $zonePrice->promo_amount = number_format($amounts,2);
                                $zonePrice->promo_msg = "";
                            }
                            else{
                                $zonePrice->promo_code = 1;
                                $zonePrice->promo_msg = "Sorry, this promo not apply";
                            }
                        }
                        else{
                            $zonePrice->promo_code = 1;
                            $zonePrice->promo_msg = "Sorry, this promo not apply";
                        }
                    } 
                    else  
                    {
                        $zonePrice->promo_code = 0;
                    }
                }
                    $zonePrice->base_price = $value->ridenow_base_price;
                    $zonePrice->distance = $distance;
                    $zonePrice->total_amount = $total_amount;
                    $zonePrice->free_waiting_time = $value->ridenow_free_waiting_time;
                    $zonePrice->waiting_charge = $value->ridenow_waiting_charge;
                    $zonePrice->price_per_time = $value->ridenow_price_per_time;
                    $zonePrice->base_distance = $value->ridenow_base_distance;
                    $zonePrice->price_per_distance = $value->ridenow_price_per_distance;
                    $zonePrice->booking_base_fare = $value->ridenow_booking_base_fare;
                    $zonePrice->booking_base_per_kilometer = $value->ridenow_booking_base_per_kilometer;
                    $zonePrice->booking_fees = $totalvalue['booking_fee'];
                    $zonePrice->outofzone = $totalvalue['outofzonefee'];

                // set surge price
                foreach($value->getSurgePrice as $key1 => $value1){
                    if($value1->start_time <= date("H:i",strtotime($data['ride_time'])) && $value1->end_time >= date("H:i",strtotime($data['ride_time'])) && in_array(date("l",strtotime($data['ride_date'])),explode(',',$value1->available_days))){
                        $total_amount = $distance * $value1->surge_price;
                        $zonePrice->price_per_distance = $value1->surge_distance_price;
                        $zonePrice->base_price = $value1->surge_price;
                        $zonePrice->distance = $distance;
                        $zonePrice->total_amount = number_format($total_amount,2);
                    }
                }

                if($totalvalue['outofzonefee'] > 0){
                    if($zonePrice->type_slug != "bajaj-auto"){
                        array_push($zone_price, $zonePrice);
                    }
                }
                else{
                    array_push($zone_price, $zonePrice);
                }

            }
            
            $datas->zone_type_price = $zone_price;

            return $this->sendResponse('Data Found',$datas,200);  
    }

    public function getCustomerDetails($slug)
    {
    	$customers = User::where('slug',$slug)->first();

        if(is_null($customers)){
    		$response = [
	            'success' => false,
	            'data'    => [],
	            'message' => "Data Not Found",
	        ];
            return response()->json($response, 200);
        }

        $trips = $this->request->where('user_id',$customers->id)->where('trip_type','LOCAL')->with('requestPlace');
        $completed_trips = $this->request->where('user_id',$customers->id)->where('trip_type','LOCAL')->where('is_completed',1);
        $cancelled_trips = $this->request->where('user_id',$customers->id)->where('trip_type','LOCAL')->where('is_cancelled',1);

        if(!auth()->user()->hasRole('Super Admin')){
            $trips = $trips->where('created_by',auth()->user()->id);
            $completed_trips = $completed_trips->where('created_by',auth()->user()->id);
            $cancelled_trips = $cancelled_trips->where('created_by',auth()->user()->id);
        }

        $customers->trips = $trips->limit(3)->get();
        $customers->completed_trips = $completed_trips->count();
        $customers->cancelled_trips = $cancelled_trips->count();

        $response = [
	        'success' => true,
	        'data'    => $customers,
	        'message' => "Data Found",
	    ];
        return response()->json($response, 200);
    }

    public function createDispatchRequest(Request $request)
    {
        // dd($request->all());
        $create_id = NULL;
        if(!auth()->user()->hasRole("Super Admin")){
            $create_id = auth()->user()->id;
        }
        if($request->ride_date_time && $request->trip_type == "RIDE_LATER"){
            $one_hour = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');
            // dump($one_hour);
            // dd(date("Y-m-d H:i:s",strtotime($request->ride_date_time)));
            if(date("Y-m-d H:i:s",strtotime($request->ride_date_time)) < $one_hour){
                return $this->sendError('Set time the after 30 minutes for current time',[],403);
            }
        }

        $user = User::where('phone_number',$request->customer_number)->first();

        if(!$user){
            $user = User::create([
                'firstname' => $request['customer_name'],
                'phone_number' => $request['customer_number'],
                // 'address' => $request['customer_address'],
            ]);
        

            $user->assignRole('user');
            $client = new OauthClients();
            $client->user_id = $user->id;
            $client->name =  $user->firstname;
            $client->secret = $this->generateRandomString(40);
            $client->redirect = 'http://localhost';
            $client->personal_access_client = false;
            $client->password_client = false;
            $client->revoked = false;
            $client->save();
        }
        
        $user_details = $user;

        // Check if the user has registred a trip already
        $this->validateUserInTrip($user);
        // Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
        $this->validateUserRequestedTrip($user);
        // Send sms to driver details 
        session(["user_details"=>$user]);

        if ($request->has('trip_types') && $request->trip_types == 'OUTSTATION') {
            return (new DispatcherRideLaterController())->outstationRide($request,$user,$create_id);
        }

        $zone = $this->getZone($request->pickup_lat, $request->pickup_lng);  
        if (!$zone) {
            return $this->sendError('Service not available at this location',[],403);
        }

        if ($request->has('trip_types') && $request->trip_types == 'RENTAL') {
            return (new DispatcherRideLaterController())->rentalRide($request,$user,$create_id);
        }   
        // if ($request->has('trip_types') && $request->trip_types == 'LOCAL') {
        //     $zone =  $this->getZone($request['drop_lat'],$request['drop_lng']);

        //     if($zone == null)
        //     {
        //         return $this->sendError('Please choose outstation trip',[],403);
        //     }
        // }  

        // Validate payment option for the trip
        $paymentOpt = $this->validatePaymentOption($request);
        // find zone
        if(!$request->has('type')){
            return $this->sendError('Vechile Type is Required',[],403);
        }
        $type = Vehicle::where('slug',$request->type)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }
        // $zone = $this->getZone($request->pickup_lat, $request->pickup_lng);  
        // if (!$zone) {
        //     return $this->sendError('Service not available at this location',[],403);
        // }
        $zone_type_id = 0;
        foreach($zone->getZonePrice as $zoneprice){               
            if($zoneprice->type_id == $type->id){
                $zone_type_id = $zoneprice->id;
            }
        }

        if ($request->has('trip_type') && $request->trip_type == 'RIDE_LATER') {
            return (new DispatcherRideLaterController())->rideLater($request,$zone,$user,$zone_type_id,$create_id);
        }
        $promocode_id =0;
        if (request()->has('promo_code') && $request['promo_code'] != ""){
            $promocode = Promocode::whereStatus(true)->where('promo_code', $request['promo_code'])->first();
            if(is_null($promocode))
                return $this->sendError('Wrong Promo Code',[],403);
            
            if($promocode->select_offer_option == 4 && $promocode->from_date > date('Y-m-d') || $promocode->select_offer_option == 4 && $promocode->to_date < date('Y-m-d'))
                return $this->sendError('Wrong Promo Code',[],403);
            $promocode_id = $promocode->id;
            $promo_count = $this->request->where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
            if($promo_count >= $promocode->promo_user_reuse_count && $promocode->select_offer_option != 1)
                return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);
            
            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($type->id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }

            $promo_all_count = $this->request->where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count >= $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }
        // dd($promocode_id);
        $request_detail = $this->request->leftJoin('request_places','request_places.request_id','=','requests.id')->where('requests.if_dispatch',1)->where('requests.user_id',$user->id)->where('pick_lat',$request->pickup_lat)->where('pick_lng',$request->pickup_lng)->where('drop_lat',$request->drop_lat)->where('drop_lng',$request->drop_lng)->where('requests.is_later',0)->where('requests.manual_trip',$request->manual_trip)->where('requests.is_trip_start',0)->where('requests.is_driver_started',0)->where('requests.is_cancelled',0)->whereNull('requests.driver_id')->select('requests.*')->first();
        if(!$request_detail){
            $requestNumber = generateRequestNumber();
            $request_params = [
                'request_number'          => $requestNumber,
                'if_dispatch'             => true,
                'request_otp'             => rand(1111, 9999),
                'user_id'                 => $user->id,
                'zone_type_id'            => $zone_type_id,
                'payment_opt'             => "Cash",
                'unit'                    => $zone->unit,
                'promo_id'                => $promocode_id,
                'requested_currency_code' => $zone->getCountry->currency_code,
                'requested_currency_symbol' => $zone->getCountry->currency_symbol,
                // 'driver_info'             => $request->additional_info,
                'trip_type'                 => $request->trip_types,
                'manual_trip'             => $request->manual_trip,
                'driver_notes'            => $request->driver_notes,
                'trip_start_time'         => NOW(),
                'created_by'              => $create_id,
                'destination_type' => $request->has('drop') && $request->has('drop_lat') && $request->has('drop_lng') ? 'NORMAL' : 'OPEN',
                'amount' => $request->trip_amount
            ];

            // dd($request_params);
                
            $request_detail = $this->request->create($request_params);

            // dd($request_detail);
            // request place detail params
            $request_place_params = [
                'pick_lat'     => $request->pickup_lat,
                'pick_lng'     => $request->pickup_lng,
                'drop_lat'     => $request->drop_lat,
                'drop_lng'     => $request->drop_lng,
                'pick_address' => $request->pickup,
                'drop_address' => $request->drop,
                'pick_up_id'   => $request->pickup_lng_id,
                'drop_id'   => $request->drop_lng_id,
                'stop_lat' => $request->stop_lat,
                'stop_lng' => $request->stop_lng,
                'stop_id'  => $request->stop_lng_id,
                'stop_address' => $request->stop,
                'stops' => $request->stop ? 1 : 0
            ];

            $request_detail->requestPlace()->create($request_place_params);

            $request_history_params = [
                'olat'         => $request->pickup_lat,
                'olng'         => $request->pickup_lng,
                'dlat'         => $request->drop_lat,
                'dlng'         => $request->drop_lng,
                'pick_address' => $request->pickup,
                'drop_address' => $request->drop
            ];
            $request_detail->requestHistory()->create($request_history_params);

            Customer::create([
                'request_id' => $request_detail->id,
                'customer_name' => $request['customer_name'],
                'customer_number' => $request['customer_number'],
                // 'customer_address' => $request['customer_address'],
                'customer_slug' => $request['customer_slug'],
                'status' => 1,
            ]);
        }
        else{
            $request_detail->zone_type_id = $zone_type_id;
            $request_detail->save();
        }

        if($request->manual_trip == 'MANUAL'){
          
            $selected_drivers = array();
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
                //    dd($drivers);
            if ($drivers->success == true) {
                $noval =0;
                foreach ($drivers->data as $key => $value) {
                    // dd($value);
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        // dump($drivers_list);
                        $selected_drivers[$key] = $drivers_list;
                    }
                }
               
            }
            else{
                // $request_detail->is_cancelled = true;
                // $request_detail->cancelled_at = NOW();
                // $request_detail->cancel_method = 'Automatic';
                // $request_detail->save();
    
                return $this->sendError('No Driver Found',$request_detail,404);  
            }
            $data = new \stdCLass();

            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            // dd($data->drivers);
            return $this->sendResponse('Data Found', $data, 200);
        }
           
        $selected_drivers = [];
        $result = fractal($request_detail, new TripRequestTransformer);

        $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
        
        $drivers = json_decode($drivers->getContent());
        // dd($drivers);
        if ($drivers->success == true) {
            $noval = 0;
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->first();
                if($driverdet){
                    $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                    if($driverdet->active && $metta == 0){
                        $selected_drivers[$noval]["user_id"] = $user->id;
                        $selected_drivers[$noval]["driver_id"] = $driverdet->id;
                        $selected_drivers[$noval]["active"] = ($key == 0 ? 1 : 0);
                        $selected_drivers[$noval]["request_id"] = $request_detail->id;
                        $selected_drivers[$noval]["assign_method"] = 1;
                        $selected_drivers[$noval]["created_at"] = date('Y-m-d H:i:s');
                        $selected_drivers[$noval]["updated_at"] = date('Y-m-d H:i:s');
                        $noval++;
                    }
                }
            }

            // dd($selected_drivers);
            if(count($selected_drivers) == 0){
                // $request_detail->is_cancelled = true;
                // $request_detail->cancelled_at = NOW();
                // $request_detail->cancel_method = 'Automatic';
                // $request_detail->save();
                return $this->sendError('No Driver Found',$request_detail,404);  
            }
        }else{
            // $request_detail->is_cancelled = true;
            // $request_detail->cancelled_at = NOW();
            // $request_detail->cancel_method = 'Automatic';
            // $request_detail->save();

            return $this->sendError('No Driver Found',$request_detail,404);  
        }

        
        foreach ($selected_drivers as $key => $selected_driver) {
            $metaDriver = User::where('id',$selected_driver['driver_id'])->first();
            
            $title = 'New Trip Requested 😊️';
            $body = 'New Trip Requested, you can accept or Reject the request';
            $sub_title = 'New Trip Requested, you can accept or Reject the request';


            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CREATED;
            $socket_data->result = $result;

            $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
            sendSocketData($socketData);

            // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
                // dd($metaDriver);
            // dispatch(new SendPushNotification($title, $sub_title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));
            sendPush($title, $sub_title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1);

            $request_meta = $request_detail->requestMeta()->create($selected_driver);   
        }

        return $this->sendResponse('Data Found', $result, 200);
    }

    public function validateUserInTrip($user)
    {
        // dd($user);
        $user_exists_trip = $this->request->where('is_completed', 0)->where('is_cancelled', 0)->where('user_id', $user->id)->where('is_later', 0)->exists();

        if ($user_exists_trip) {
            return $this->sendError('User already in trip',[],400);
        }
    }

    public function validateUserRequestedTrip($user)
    {
        $request_meta_with_current_user = RequestMeta::where('user_id', $user->id);

        if ($request_meta_with_current_user->exists()) {
            // get request detail
            $request_with_user = $request_meta_with_current_user->pluck('request_id')->first();
            if ($request_with_user) {
                $this->request->where('id', $request_with_user)->update(['is_cancelled'=>1,'cancel_method'=>1]);
            }
            // Delete all meta details
            $request_meta_with_current_user->delete();
        }
    }

    public function validatePaymentOption($request)
    {
        switch ($request->payment_opt) {
            case "CARD": // Card payment
                return $this->checkCard($request);
                break;
            case "CASH": // Cash payment
                return true;
                break;
            case "WALLET": // Wallet payment
                return $this->checkWallet($request);
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

    public function dispatchRequestView($ride)
    {
        $request = $this->request->where('id',$ride)->first();
        $outstation_trip = OutstationUploadImages::where('request_id',$ride)->first();
        $types = Vehicle::where('status',1)->get();
        $outstations = OutstationPriceFixing::where('status',1)->get();

        return view('taxi.dispatcher.DispatcherRequestView',['request' => $request,'outstation_trip' => $outstation_trip,'types'=>$types,'outstations' => $outstations]); 
    }

    public function getDispatchRequest($ride)
    {
        $request = $this->request->where('id',$ride)->first();
        $metta = RequestMeta::where('request_id',$ride)->first();

        // $driverdet = User::where('id',$metta->driver_id)->first();

        if($metta){
            return $this->sendResponse('Driver searching please wait', $request, 200);
        }
        elseif($request->is_driver_started == 1){
            return $this->sendResponse('Driver accepted your trip', $request, 200);
        }
        else{
            // $request->is_cancelled = true;
            // $request->cancelled_at = NOW();
            // $request->cancel_method = 'Automatic';
            // $request->save();
            return $this->sendResponse('No Driver Found',$request,404); 
        }
    }

    public function dispatcherTripList(Request $request)
    {
        $requests_now = RequestModel::where('is_later',0)->where('if_dispatch',1)->where('trip_type','LOCAL')->whereNotNull('driver_id')->where('is_cancelled',1)->orWhere(function($query) {
            $query->whereNotNull('driver_id')
                  ->where('is_cancelled',0)->where('is_later',0)->where('if_dispatch',1)->where('trip_type','LOCAL');
        })->orderby('created_at','desc');

        $requests_later = RequestModel::where('is_later',1)->where('if_dispatch',1)->where('trip_type','LOCAL')->orderby('created_at','desc');

        $requests_rental_now = RequestModel::where('is_later',0)->where('if_dispatch',1)->where('trip_type','RENTAL')->whereNotNull('driver_id')->where('is_cancelled',1)->orderby('created_at','desc')->orWhere(function($query) {
            $query->whereNotNull('driver_id')
                  ->where('is_cancelled',0)->where('is_later',0)->where('if_dispatch',1)->where('trip_type','RENTAL');
        });

        $requests_rental_later = RequestModel::where('is_later',1)->where('if_dispatch',1)->where('trip_type','RENTAL')->orderby('created_at','desc');

        $oustation_list = RequestModel::where('if_dispatch',1)->where('trip_type','OUTSTATION')->orderby('created_at','desc');

        if(!auth()->user()->hasRole('Super Admin')){
            $requests_now = RequestModel::where('is_later',0)->where('if_dispatch',1)->where('trip_type','LOCAL')->whereNotNull('driver_id')->where('is_cancelled',1)->where('created_by',auth()->user()->id)->orWhere(function($query) {
                $query->whereNotNull('driver_id')
                      ->where('is_cancelled',0)->where('is_later',0)->where('if_dispatch',1)->where('trip_type','LOCAL');
                if(!auth()->user()->hasRole('Super Admin'))
                    $query->where('created_by',auth()->user()->id);
            })->orderby('created_at','desc');
            $requests_later = $requests_later->where('created_by',auth()->user()->id);
            $requests_rental_now = RequestModel::where('is_later',0)->where('if_dispatch',1)->where('trip_type','RENTAL')->whereNotNull('driver_id')->where('is_cancelled',1)->orderby('created_at','desc')->where('created_by',auth()->user()->id)->orWhere(function($query) {
                $query->whereNotNull('driver_id')
                      ->where('is_cancelled',0)->where('is_later',0)->where('if_dispatch',1)->where('trip_type','RENTAL');
            });
            $requests_rental_later = $requests_rental_later->where('created_by',auth()->user()->id);
            $oustation_list = $oustation_list->where('created_by',auth()->user()->id);
        }

        $requests_now = $requests_now->get();
        $requests_later = $requests_later->get();
        $requests_rental_now = $requests_rental_now->get();
        $requests_rental_later = $requests_rental_later->get();
        $oustation_list = $oustation_list->get();

        return view('taxi.dispatcher.DispatcherTripList',['requests_now' => $requests_now, 'requests_later' => $requests_later,'requests_rental_now' => $requests_rental_now, 'requests_rental_later' => $requests_rental_later,'oustation_list' => $oustation_list]);
    }

    public function searchDriver($ride)
    {

        $request = $this->request->where('id',$ride)->first();

        $zone_type = ZonePrice::where('id',$request->zone_type_id)->first();

        $selected_drivers = [];

        $drivers = fetchDrivers($request->requestPlace->pick_lat,$request->requestPlace->pick_lng,$zone_type->type_id);
        $drivers = json_decode($drivers->getContent());

        if ($drivers->success == true) {
            $noval = 0;
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->first();
                $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                if($driverdet->active && $metta == 0){
                    $selected_drivers[$noval]["user_id"] = $user->id;
                    $selected_drivers[$noval]["driver_id"] = $driverdet->id;
                    $selected_drivers[$noval]["active"] = ($key == 0 ? 1 : 0);
                    $selected_drivers[$noval]["request_id"] = $request->id;
                    $selected_drivers[$noval]["assign_method"] = 1;
                    $selected_drivers[$noval]["created_at"] = date('Y-m-d H:i:s');
                    $selected_drivers[$noval]["updated_at"] = date('Y-m-d H:i:s');
                    $noval++;
                }
            }
            
            if(count($selected_drivers) == 0){
                $request->is_cancelled = true;
                $request->cancelled_at = NOW();
                $request->cancel_method = 'Automatic';
                $request->save();

                return $this->sendResponse('No Driver Found',$request,200);   
            }
        }else{
            $request->is_cancelled = true;
            $request->cancelled_at = NOW();
            $request->cancel_method = 'Automatic';
            $request->save();

            return $this->sendResponse('No Driver Found',$request,200);  
        }
        $request->hold_status = 0;
        $request->save();
        // $metaDriverslug = $selected_drivers[0]['driver_id'];

        $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
         
        $result = fractal($request, new TripRequestTransformer);
        // $result['request_number'] = $request->request_number;
        $title = 'New Trip Requested 😊️';
        $body = 'New Trip Requested, you can accept or Reject the request';
        $sub_title = 'New Trip Requested, you can accept or Reject the request';


        $socket_data = new \stdClass();
        $socket_data->success = true;
        $socket_data->success_message  = PushEnum::REQUEST_CREATED;
        $socket_data->result = $result;

        $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
        sendSocketData($socketData);

        // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];
        $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];

        dispatch(new SendPushNotification($title,$sub_title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));

        // dd($selected_drivers);
        foreach ($selected_drivers as $key => $selected_driver) {
            $request_meta = $request->requestMeta()->create($selected_driver);   
        }

        return $this->sendResponse('Data Found', $result, 200);
    }

    public function getDriverDetails($slug,$number)
    {
        $request = $this->request->where('id',$slug)->first();
        if($request->trip_type == "OUTSTATION"){
            $zone_type = OutstationPriceFixing::where('id',$request->outstation_type_id)->first();
        }
        elseif($request->trip_type == "RENTAL"){
            $zone_type = PackageItem::where('id',$request->package_item_id)->first();
        }
        else{
            $zone_type = ZonePrice::where('id',$request->zone_type_id)->first();
        }

        $selected_drivers = [];
        $vehicle = Vehicle::where('id',$zone_type->type_id)->first();
        $drivers = fetchDriversRadius($request->requestPlace->pick_lat,$request->requestPlace->pick_lng,$vehicle->slug,$request->trip_type,500);
        $drivers = json_decode($drivers->getContent());
        if ($drivers->success == true) {
            $noval = 0;
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->where('phone_number','like','%'.$number.'%')->first();
                if($driverdet){
                    $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                    if($driverdet->active && $metta == 0){
                        $selected_drivers[$noval] = $driverdet;
                        $noval++;
                    }
                }
            }
            
            if(count($selected_drivers) == 0){
                return $this->sendError('No Driver Found',$request,404);   
            }
        }else{
            return $this->sendError('No Driver Found',$request,404);  
        }
        return $this->sendResponse('Driver Found',$selected_drivers,200);  
        // if(is_null($selected_drivers)){
    	// 	$response = [
	    //         'success' => false,
	    //         'data'    => [],
	    //         'message' => "Data Not Found",
	    //     ];
        //     return response()->json($response, 200);
        // }
        // else{
        //    	$response = [
	    //         'success' => true,
	    //         'data'    => $selected_drivers,
	    //         'message' => "Data Found",
	    //     ];
        //     return response()->json($response, 200);
        // }
    }

    public function assignDriver($ride,$driver)
    {
        $request_detail = RequestModel::where('id',$ride)->first();

        $message = 'trip_accepted';

        $updated_params = [
            'driver_id'         => $driver,
            // 'accepted_at'       => now(),
            // 'is_driver_started' => 1,
            'hold_status'       => 1,
            'created_by'        => auth()->user()->id
        ];

        $request_detail->update($updated_params);
        
        
        
        RequestMeta::where('request_id', $ride)->delete();
        // dd($request_detail->user_id);
        // send otp user details
        $userDetails = User::find($request_detail->user_id);

        $driverDetails = User::where('id',$driver)->first();

        $car_details = Driver::where('user_id',$driver)->first();

        if($request_detail->is_later == 0){

            $driver_details = Driver::where('user_id',$driver)->first();
                
            // Update the driver's available state as false
            $driver_details->is_available = false;
            $driver_details->total_accept += 1;
            $driver_details->reject_count = 0;
            $driver_details->save();

            $request_result =  fractal($request_detail, new TripRequestTransformer);
                    
            if ($request_detail->user_id != null) {
                $push_request_detail = $request_result->toJson();
                $userModel = User::find($request_detail->user_id);
                //dd($userModel->device_info_hash);
                $metaDriver = User::where('id',$driver)->first();
                // dd($metaDriver);

                $title = 'New Trip Requested 😊️';
                $body = 'New Trip Requested, you can accept or Reject the request';
                $sub_title = 'New Trip Requested, you can accept or Reject the request';


                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = PushEnum::REQUEST_CREATED;
                $socket_data->result = $request_result;

                $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
                sendSocketData($socketData);

                // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];
                $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];

                dispatch(new SendPushNotification($title,$sub_title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));
            }
        }
                $otp = $this->UniqueRandomNumbers(4);
                
               
                    //  $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$userDetails->phone_number.'&message=Hi '.$userDetails->firstname.', your booking is confirmed with us. Your OTP for the ride is '.$otp.'. Driver name : '.$driverDetails->firstname.''.$driverDetails->lastname.' Phone number: '.$driverDetails->phone_number.' Vehicle Number: '.$car_details->car_number.' . Thank you for using our taxi service . Our City Our Taxi !!! - NPTECH.');

                    $otp_update = RequestModel::where('id',$ride)->first();
            
                    $updated_params_otp = [
                        'request_otp'         => $otp,
                    ];
            
                    $otp_update->update($updated_params_otp);
                    
                    // $post = json_decode($data->getBody()->getContents());
            

        return $this->sendResponse('Driver Assign Successfully', $request_detail, 200);
    }

    public function dispatchTripCancel($ride)
    {
      
        $request_detail = RequestModel::where('id',$ride)->first();

        // dd($request_detail);

        // if($request_detail->is_cancelled == 1){
        //     session()->flash('message',"Already this trip cancelled");
        //     return back();
        // }
        // if($request_detail->is_driver_started == 1){
        //     session()->flash('message',"Already driver accepted to this trip");
        //     return back();
        // }
        $request_detail->cancelled_at = NOW();
        $request_detail->is_cancelled = 1;
        $request_detail->cancel_method = CancelMethod::DISPATCHER_TEXT;
        $request_detail->hold_status = 0;
        $request_detail->save();

        $request_result =  fractal($request_detail, new TripRequestTransformer);
                
        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);
            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'trip-cancel');
            if(is_null($push_data)){
                $title = 'Trip Cancelled By Dispatcher';
                $body = 'Trip cancelled by dispatcher panel';
                $sub_title = 'Trip cancelled by dispatcher panel';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;
            } 

            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DISPATCHER;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DISPATCHER];

            dispatch(new SendPushNotification($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
        }


        if ($request_detail->driver_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->driver_id);
            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'trip-cancel');
            if(is_null($push_data)){
                $title = 'Trip Cancelled By Dispatcher';
                $body = 'Trip cancelled by dispatcher panel';
                $sub_title = 'Trip cancelled by dispatcher panel';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;
            } 

            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DISPATCHER;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DISPATCHER];

            dispatch(new SendPushNotification($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
        }

        
        return back();
    }

    public function dispatcherEdit($ride)
    {
        $request_detail = RequestModel::where('id',$ride)->first();

        $package_detail = PackageMaster::where('status',1)->get();
        $zone = Zone::where('zone_name','Coimbatore')->where('status',1)->first();

        $outstanding_pickup = OutstationMaster::where('status',1)->groupby('pick_up')->get();
        $outstanding_drops = OutstationMaster::where('status',1)->groupby('drop')->get();
        $outstation_price = OutstationPriceFixing::where('status',1)->get();

        return view('taxi.dispatcher.DispatcherEditTrip',['request_detail' => $request_detail,'package_detail' => $package_detail,'outstanding_pickup' => $outstanding_pickup,'outstanding_drops' => $outstanding_drops,'outstation_price' => $outstation_price,'zone' => $zone]);
    }

    public function editDispatchRequest(Request $request)
    {

        // dd($request->all());
        $user = User::where('slug',$request->customer_slug)->first();

        // Check if the user has registred a trip already
        $this->validateUserInTrip($user);
        // Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
        $this->validateUserRequestedTrip($user);
        // Validate payment option for the trip

        $zone = $this->getZone($request->pickup_lat, $request->pickup_lng);
        if (!$zone) {
            return $this->sendError('Service not available at this location',[],403);
        }

        if ($request->has('trip_types') && $request->trip_types == 'RENTAL') {
            return (new DispatcherRideLaterController())->editRentalRide($request,$user);
        }
        if ($request->has('trip_types') && $request->trip_types == 'OUTSTATION') {
            return (new DispatcherRideLaterController())->editOutstationRide($request,$user);
        }

        $paymentOpt = $this->validatePaymentOption($request);
        // find zone
        $type = Vehicle::where('slug',$request->type)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }
        $zone_type_id = 0;
        foreach($zone->getZonePrice as $zoneprice){               
            if($zoneprice->type_id == $type->id){
                $zone_type_id = $zoneprice->id;
            }
        }

        if($request->has('package')){
            $package = PackageMaster::where('slug',$request->package)->where('status',1)->first();
            $rental_package = $package->id;
        }
        else{
            $rental_package = 0;
        }

        if ($request->has('trip_type') && $request->trip_type == 'RIDE_LATER') {
            return (new DispatcherRideLaterController())->editRideLater($request,$zone,$user,$zone_type_id,$rental_package);
        }
        $promocode_id =0;
        if (request()->has('promo_code') && $request['promo_code'] != ""){
            $promocode = Promocode::whereStatus(true)->where('promo_code', $request['promo_code'])->first();
            if(is_null($promocode))
                return $this->sendError('Wrong Promo Code',[],403);
            
            if($promocode->select_offer_option == 4 && $promocode->from_date > date('Y-m-d') || $promocode->select_offer_option == 4 && $promocode->to_date < date('Y-m-d'))
                return $this->sendError('Wrong Promo Code',[],403);
            $promocode_id = $promocode->id;
            $promo_count = $this->request->where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
            if($promo_count >= $promocode->promo_user_reuse_count)
                return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);
            
            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($type->id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }

            $promo_all_count = $this->request->where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count >= $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }
        // dd($promocode_id);
        // $requestNumber = generateRequestNumber();
        $request_params = [
            'is_later'                => false,
            'if_dispatch'             => true,
            'ride_type'				  => 'Ride Now',
            'request_otp'             => 1234, //rand(1111, 9999),
            'user_id'                 => $user->id,
            'zone_type_id'            => $zone_type_id,
            'payment_opt'             => 'Cash',
            'unit'                    => $zone->unit,
            'promo_id'                => $promocode_id,
            'requested_currency_code' => $zone->getCountry->currency_code,
            'requested_currency_symbol' => $zone->getCountry->currency_symbol,
            'trip_type'                 => $request->trip_types,
            'driver_notes'            => $request->driver_notes,
            'manual_trip'             => $request->manual_trip,
            'trip_start_time'         => NOW(),
        ];

        if($request->manual_trip == "AUTOMATIC"){
            $request_params['driver_id'] = NULL;
        }
            
        $request_detail = $this->request->where('id',$request->ride_id)->update($request_params);

        $request_detail = $this->request->where('id',$request->ride_id)->first();

        // request place detail params
        $request_place_params = [
            'pick_lat'     => $request->pickup_lat,
            'pick_lng'     => $request->pickup_lng,
            'drop_lat'     => $request->drop_lat,
            'drop_lng'     => $request->drop_lng,
            'pick_address' => $request->pickup,
            'drop_address' => $request->drop,
            'pick_up_id'   => $request->pickup_lng_id,
            'drop_id'   => $request->drop_lng_id,
            'stop_lat' => $request->stop_lat,
            'stop_lng' => $request->stop_lng,
            'stop_address' => $request->stop,
            'stops' => $request->stop ? 1 : 0
        ];

        $request_detail->requestPlace()->update($request_place_params);

        $request_history_params = [
            'olat'         => $request->pickup_lat,
            'olng'         => $request->pickup_lng,
            'dlat'         => $request->drop_lat,
            'dlng'         => $request->drop_lng,
            'pick_address' => $request->pickup,
            'drop_address' => $request->drop
        ];
        $request_detail->requestHistory()->update($request_history_params);

        Customer::where('request_id',$request_detail->id)->update([
            'customer_name' => $request['customer_name'],
            'customer_number' => $request['customer_number'],
            // 'customer_address' => $request['customer_address'],
            'customer_slug' => $request['customer_slug'],
            'status' => 1,
        ]);

        if($request->manual_trip == 'MANUAL'){
          
            $selected_drivers = array();
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
                //    dd($drivers);
            if ($drivers->success == true) {
                // dd($drivers->data);
                $noval =0;
                foreach ($drivers->data as $key => $value) {
                    // dd($value);
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    $drivers_list->trip_complete_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                    $drivers_list->trip_cancel_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                    $drivers_list->trip_today_complete_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                    $drivers_list->trip_today_cancel_count = $this->request->where('driver_id',$drivers_list->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                    // dump($drivers_list);
                    $distance = $value->distance / 1000 / 50;
                    $time = (int)$distance * 60;
                    if($time == 0){
                        $time = 3;
                    }
                    $hours = $time / 60;
                    $minite = $time % 60;

                    $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                    $selected_drivers[$key] = $drivers_list;
                }
               
            }
            else{
                // $request_detail->is_cancelled = true;
                // $request_detail->cancelled_at = NOW();
                // $request_detail->cancel_method = 'Automatic';
                // $request_detail->save();
    
                return $this->sendError('No Driver Found',$request_detail,404);  
            }
            $data = new \stdCLass();

            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            // dd($data->drivers);
            return $this->sendResponse('Data Found', $data, 200);
        }
           
        $selected_drivers = [];

        $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type);
        $drivers = json_decode($drivers->getContent());

        if ($drivers->success == true) {
            $noval = 0;
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->first();
                $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                if($driverdet->active && $metta == 0){
                    $selected_drivers[$noval]["user_id"] = $user->id;
                    $selected_drivers[$noval]["driver_id"] = $driverdet->id;
                    $selected_drivers[$noval]["active"] = ($key == 0 ? 1 : 0);
                    $selected_drivers[$noval]["request_id"] = $request_detail->id;
                    $selected_drivers[$noval]["assign_method"] = 1;
                    $selected_drivers[$noval]["created_at"] = date('Y-m-d H:i:s');
                    $selected_drivers[$noval]["updated_at"] = date('Y-m-d H:i:s');
                    $noval++;
                }
            }
            if(count($selected_drivers) == 0){
                // $request_detail->is_cancelled = true;
                // $request_detail->cancelled_at = NOW();
                // $request_detail->cancel_method = 'Automatic';
                // $request_detail->save();

                return $this->sendResponse('No Driver Found',$request_detail,200);  
            }
        }else{
            // $request_detail->is_cancelled = true;
            // $request_detail->cancelled_at = NOW();
            // $request_detail->cancel_method = 'Automatic';
            // $request_detail->save();

            return $this->sendResponse('No Driver Found',$request_detail,200);  
        }

        // $metaDriverslug = $selected_drivers[0]['driver_id'];

        $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
         
        $result = fractal($request_detail, new TripRequestTransformer);
        // $result['request_number'] = $request_detail->request_number;
        $title = 'New Trip Requested 😊️';
        $body = 'New Trip Requested, you can accept or Reject the request';
        $sub_title = 'New Trip Requested, you can accept or Reject the request';


        $socket_data = new \stdClass();
        $socket_data->success = true;
        $socket_data->success_message  = PushEnum::REQUEST_CREATED;
        $socket_data->result = $result;

        $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
        sendSocketData($socketData);

        // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];
        $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];

        dispatch(new SendPushNotification($title,$sub_title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));

        // dd($selected_drivers);
        foreach ($selected_drivers as $key => $selected_driver) {
            $request_meta = $request_detail->requestMeta()->create($selected_driver);   
        }

        return $this->sendResponse('Data Found', $result, 200);
    }

    public function getRentalPackage($slug)
    {
        $package_detail = PackageMaster::where('status',1)->where('slug',$slug)->first();

        if (!$package_detail) {
            return $this->sendError('Data not found',[],403);
        }

        $package_detail_items = PackageItem::with('getVehicle')->where('package_id',$package_detail->id)->get();

        return $this->sendResponse('Data Found', $package_detail_items, 200);
    }

    public function getRentalPackageEta(Request $request)
    {
        $data = $request->all();
        $zone = $this->getZone($data['pickup_lat'], $data['pickup_lng']);

        if(is_null($zone))
            return $this->sendError('Non services zone',[],404);

        // if($zone->non_service_zone == 'Yes'){
        //     return $this->sendError('Non services zone',[],404);
        // }
        if($data['package'] != ""){
            $package_detail = PackageMaster::where('status',1)->where('slug',$data['package'])->first();

            if (!$package_detail) {
                return $this->sendError('Data not found',[],403);
            }

            $package_detail_items = PackageItem::with('getVehicle','getPackage','getPackage.getCountry')->where('id',$data['type'])->first();

            if (!$package_detail_items) {
                return $this->sendError('Data not found',[],403);
            }

            if (request()->has('promo_code') && $request['promo_code'] != "") {
                $expired = Promocode::whereStatus(true)->where('promo_code', $request['promo_code'])->first();
                // dd($expired);
                if(!$expired || $expired->select_offer_option == 4 && $expired->from_date > date('Y-m-d') || $expired->select_offer_option == 4 && $expired->to_date < date('Y-m-d') ){
                    return $this->sendError('Invalid Prome code',[],404);
                }
                if ($expired->promo_code == $request['promo_code']) {
                    if(in_array($request->type,$expired->types)){
                        $total_amount = $package_detail_items->price;
                        if($total_amount > $expired->target_amount){
                            $package_detail_items->promo_code = 1;

                            $total_amounts = $this->promoCalculation($expired,$total_amount);

                            $package_detail_items->price = $total_amounts;
                            $total_amounts = str_replace(',', '', $total_amounts);
                            $amounts = (double) $total_amount - (double) $total_amounts;
                            $package_detail_items->promo_price = number_format($amounts,2);
                            $package_detail_items->total_price = number_format($total_amount,2);
                            $package_detail_items->promo_msg = "";
                        }
                        else{
                            $package_detail_items->promo_code = 1;
                            $package_detail_items->promo_msg = "Sorry, this promo not apply";
                        }
                    }
                    else{
                        $package_detail_items->promo_code = 1;
                        $package_detail_items->promo_msg = "Sorry, this promo not apply";
                    }
                } 
                else  
                {
                    $package_detail_items->promo_code = 0;
                    $package_detail_items->promo_msg = "Sorry, Invalid Promo";
                }
            }

            return $this->sendResponse('Data Found', $package_detail_items, 200);
        }
    }

    public function getOutstationEta(Request $request) 
    {
        $outstanding = OutstationMaster::with('getCountry')->where('status',1)->where('drop',$request->drop)->first();
        $outstation_price = OutstationPriceFixing::where('status',1)->where('id',$request->type)->first();
        $datas = $request->all();
        // $distances = $outstanding->distance;
        $distances = $this->getDistance($datas['pickup_lat'],$datas['pickup_lng'],$outstanding->drop_lat,$outstanding->drop_lng);
        $data = array();
        $data['currency_symbol'] = $outstanding->getCountry->currency_symbol;
        $data['distance'] = $distances;
        $data['price'] = number_format($outstation_price->distance_price,2);
        if($datas['way_trip'] == "ONE"){
            $driver_price = $outstation_price->driver_price;
            $total = $distances * $outstation_price->distance_price * 2 + $driver_price;
        }
        else{            
            $data['price'] = number_format($outstation_price->distance_price_two_way,2);
            if($request->has('ride_date_time')){
                // dd($datas['ride_return_date_time']);
                // $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', '2022-07-09 14:47:02');
                // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', '2022-07-10 14:47');

                $from = Carbon::parse(date('Y-m-d H:i:s', strtotime($datas['ride_date_time'])));
                $to = Carbon::parse(date('Y-m-d H:i:s', strtotime($datas['ride_return_date_time'])));
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $from);


                $diff_in_hours = $to->diffInHours($from);
                $distances = $distances*2;
                $driverSinglePrice = $outstation_price->day_rent_two_way;
                if($diff_in_hours > 0){
                    if($diff_in_hours <= 12){
                        $driver_price = $driverSinglePrice;                       
                    }else{
                        $newdrivertime = $diff_in_hours - 12;
                        if($newdrivertime <= 24){
                            $driver_price = $driverSinglePrice * 2;
                        }else{
                            $aa = $newdrivertime / 24 ;
                            $remainder = $newdrivertime % 24 ;
                            $driver_price = ($driverSinglePrice * 2) + $driverSinglePrice ;
                            $grace_time = $outstation_price->grace_time;
                            $waiting_charge = $outstation_price->waiting_charge;
                            if($remainder < $grace_time){
                                $driver_price = +$driver_price;
                            }else{
                                $waiting_time_charge = $grace_time * $waiting_charge;
                                $driver_price  = $waiting_time_charge + $driver_price;
                            }
                        }
                    }
                }
                $distance_cost = ($distances ) * $outstation_price->distance_price_two_way;
                $total = $distance_cost + $driver_price;
            }
        }
        
        $data['driver_price'] = number_format($driver_price,2);
        $data['total'] = number_format($total,2);
        $data['promo_price'] = 0;
        $total_amounts = $total;
        if (request()->has('promo_code') && $request->promo_code != "") {
            $expired = Promocode::whereStatus(true)->where('promo_code',  $request->promo_code)->first();
            // dd($expired);
            if(!$expired || $expired->select_offer_option == 4 && $expired->from_date > date('Y-m-d') || $expired->select_offer_option == 4 && $expired->to_date < date('Y-m-d') ){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if ($expired->promo_code ==  $request->promo_code) {
                if(in_array($outstation_price->type_id,$expired->types)){
                    $total_amount = $total;
                    if($total_amount > $expired->target_amount){
                        $data['promo_code'] = 1;

                        $total_amounts = $this->promoCalculation($expired,$total_amount);
                        $total_amounts = str_replace(',', '', $total_amounts);
                        $amounts = (double) $total_amount - (double) $total_amounts;
                        $data['promo_price'] = number_format($amounts,2);
                        $data['total'] = number_format($total_amounts,2);
                        $data['promo_msg'] = "";
                    }
                    else{
                        $data['promo_code'] = 1;
                        $data['promo_price'] = 0;
                        $data['promo_msg'] = "Sorry, this promo not apply";
                    }
                }
                else{
                    $data['promo_code'] = 1;
                    $data['promo_price'] = 0;
                    $data['promo_msg'] = "Sorry, this promo not apply";
                }
            } 
            else  
            {
                $data['promo_code'] = 0;
                $data['promo_price'] = 0;
                $data['promo_msg'] = "Sorry, Invalid Promo";
            }

            
        }

        if($outstanding->hill_station == 'YES'){	
            $data['hill_station_amount'] = $outstation_price->hill_station_price;	
            $data['hill_station_status'] = 1;	
            $data['total'] = number_format($total_amounts+$outstation_price->hill_station_price,2);	
        }	
        else{	
            $data['hill_station_amount'] = 0;	
            $data['hill_station_status'] = 0;	
            $data['total'] = number_format($total_amounts,2);	
        }
        return $this->sendResponse('Data Found', $data, 200);
    }

    public function getOutstationLocation($name)
    {
        $outstanding = OutstationMaster::with('getCountry')->where('status',1)->where('drop',$name)->first();
        return $this->sendResponse('Data Found', $outstanding, 200);
    }

    public function adminTripCancel($trip)
    {
        $request_detail = RequestModel::where('id',$trip)->first();
        if($request_detail->is_cancelled == 1){
            session()->flash('message',"Already this trip cancelled");
            return back();
        }
        if($request_detail->is_driver_started == 1){
            session()->flash('message',"Already driver accepted to this trip");
            return back();
        }
        $request_detail->cancelled_at = NOW();
        $request_detail->is_cancelled = 1;
        $request_detail->cancel_method = CancelMethod::DISPATCHER_TEXT;
        $request_detail->hold_status = 0;
        $request_detail->save();

        RequestMeta::where('request_id',$request_detail->id)->delete();

        $request_result =  fractal($request_detail, new TripRequestTransformer);
                
        if ($request_detail->user_id != null) {
            $push_request_detail = $request_result->toJson();
            $userModel = User::find($request_detail->user_id);
            //dd($userModel->device_info_hash);

            $title = Null;
            $body = '';
            $lang = $userModel->language;
            $push_data = $this->pushlanguage($lang,'trip-cancel');
            if(is_null($push_data)){
                $title = 'Trip Cancelled By Dispatcher';
                $body = 'Trip cancelled by dispatcher panel';
                $sub_title = 'Trip cancelled by dispatcher panel';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 



           
            $push_data = ['notification_enum'=>PushEnum::REQUEST_CANCELLED_BY_DISPATCHER,'result'=>(string)$push_request_detail];

            // Form a socket sturcture using users'id and message with event name
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DISPATCHER;
            $socket_data->result = $request_result;

            $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
            sendSocketData($socketData);
                  
            // dispatch(new SendPushNotification($title, $push_data, $userModel->token, $userModel->mobile_application_type,0));
            
            // $pushData = ['notification_enum' => PushEnum::TRIP_ACCEPTED_BY_DRIVER, 'result' => (string) $request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DISPATCHER, 'result' => $request_result];
            
            dispatch(new SendPushNotification($title,$sub_title,$pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0));
        }
        return $this->sendResponse('Trip Cancelled', $request_detail, 200);
    }
}



