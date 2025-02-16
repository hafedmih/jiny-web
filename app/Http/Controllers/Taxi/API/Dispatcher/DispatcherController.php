<?php

namespace App\Http\Controllers\Taxi\API\Dispatcher;

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
use App\Models\taxi\Favourite;
use App\Models\taxi\OutstationUploadImages;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\taxi\Wallet;
use App\Models\taxi\RiderAddress;
use App\Models\taxi\Settings;
use DB;
use GuzzleHttp\Client;
use Spatie\Geocoder\Facades\Geocoder;

class DispatcherController extends BaseController
{
    use CommanFunctions,RandomHelper;

    public $request;

    public function __construct(RequestModel $request) {
        
        $this->request = $request;
    }

    public function checkzone(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'pickup_lat' => 'required',
                'pickup_long' => 'required',
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            $data = $request->all();

            // get zone use pickup lat and long
            $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);
            if($zone){
                if($zone->non_service_zone == 'No'){
                    $data['zone'] = true;
                }
                else{
                    $data['zone'] = false;
                }
            }else {
                $data['zone'] = false;

            }
            // // dd($zone);
            // if(is_null($zone))
            //     return $this->sendError('Non services zone',[],404);

            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }
        
        $user = User::where('email',$request->email)->where('active',true)->first();
//dd($user);
        if(is_null($user)){
            return $this->sendResponse('Data Not Found',[],200); 
        }
        $user->device_info_hash = $request->device_info_hash;
        $user->update();
        // fetch the oauth credentials
        $fetchOauth = OauthClients::where('user_id',$user->id)->first();
        if(is_null($fetchOauth)){
            return $this->sendError('No user Found',[],403);
        }
       // $data = array();
        $data['client_id'] = $fetchOauth->id;
        $data['client_secret'] = $fetchOauth->secret;
        $data['slug']= $user->slug;
        return response()
            ->json(['success' => true,'data' => $data],200);
    }

    public function getCustomer($number)
    {
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        // if (!$user->hasRole('Dispatcher approval')) {
        //     return $this->sendError('Unauthorized',[],401);    
        // }

    	$customers = User::where('phone_number','LIKE','%'.$number.'%')->role('user')->first();
        

    	if(is_null($customers)){
    		$response = [
	            'success' => false,
	            'data'    => [],
	            'message' => "Data Not Found",
	        ];
            return response()->json($response, 200);
        }
        else{
            $get_favourite_place = Favourite::where('user_id',$customers->id)->where('status',1)->get();

            if(is_null($get_favourite_place))
            {
                $customers['favourite_place'] = null;
            }
            else
            {
                $customers['favourite_place'] = $get_favourite_place;
            }
           	$response = [
	            'success' => true,
	            'data'    => array($customers),
	            'message' => "Data Found",
	        ];
            return response()->json($response, 200);
            return $this->sendResponse('Data Found',$response,200);  
        }
    }

    public function getVehicles(Request $request)
    {
        $req = [ 
            'pickup_lat' => 'required',
            'pickup_long' => 'required',
            'ride_type' => 'required',
            'trip_type' => 'required',
            'pickup_address' => 'required',
            'destination_type' => 'required',
        ];
        if($request->destination_type == 'NORMAL'){
            $req['drop_lat'] = 'required';
            $req['drop_long'] = 'required';
            $req['drop_address'] = 'required';
        }
        $validator = Validator::make($request->all(), $req);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('Dispatcher approval')) {
            return $this->sendError('Unauthorized',[],401);    
        }

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
            if($request->has('drop_address')){
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
                         if ($request->has('drop_lat')) {

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
                    if ($request->has('drop_lat')) {

                        $totalvalue = $this->etaCalculation($distance,$value->ridenow_base_distance,$value->ridenow_base_price,$value->ridenow_price_per_distance,$value->ridenow_booking_base_fare,$value->ridenow_booking_base_per_kilometer,$outofzonefee);

                        $zonePrice->base_price = $value->ridenow_base_price;
                        $zonePrice->free_waiting_time = $value->ridenow_free_waiting_time;
                        $zonePrice->waiting_charge = $value->ridenow_waiting_charge;
                        $zonePrice->price_per_time = $value->ridenow_price_per_time;
                        $zonePrice->base_distance = $value->ridenow_base_distance;
                        $zonePrice->price_per_distance = $value->ridenow_price_per_distance;
                        $zonePrice->booking_base_fare = $value->ridenow_booking_base_fare;
                        $zonePrice->booking_base_per_kilometer = $value->ridenow_booking_base_per_kilometer;                         
                    }
                    $computedDistance = $distance - $value->ridenow_base_distance;
                    if($computedDistance >= 0 && $data['destination_type'] == 'NORMAL'){
                        $zonePrice->computed_price = number_format($value->ridenow_price_per_distance * $computedDistance,2);
                        $zonePrice->computed_distance = round($computedDistance,2);
                    }
                }
                // Ride Later
                else if($data['ride_type'] == "RIDE_LATER"){
                    if ($request->has('drop_lat')) {

                    $totalvalue = $this->etaCalculation($distance,$value->ridelater_base_distance,$value->ridelater_base_price,$value->ridelater_price_per_distance,$value->ridelater_booking_base_fare,$value->ridelater_booking_base_per_kilometer,$outofzonefee);

                    $zonePrice->base_price = $value->ridelater_base_price;
                    $zonePrice->free_waiting_time = $value->ridelater_free_waiting_time;
                    $zonePrice->waiting_charge = $value->ridelater_waiting_charge;
                    $zonePrice->price_per_time = $value->ridelater_price_per_time;
                    $zonePrice->base_distance = $value->ridelater_base_distance;
                    $zonePrice->price_per_distance = $value->ridelater_price_per_distance;
                    $zonePrice->booking_base_fare = $value->ridelater_booking_base_fare;
                    $zonePrice->booking_base_per_kilometer = $value->ridelater_booking_base_per_kilometer;
                    }
                    $computedDistance = $distance - $value->ridelater_base_distance;
                    if($computedDistance >= 0 && $data['destination_type'] == 'NORMAL'){
                        $zonePrice->computed_price = number_format($value->ridelater_price_per_distance * $computedDistance,2);
                        $zonePrice->computed_distance = round($computedDistance,2);
                    }
                }

                if($data['destination_type'] == 'OPEN'){
                    $outofzonefee=0;
                    $totalvalue = $this->etaCalculation($distance,$value->open_base_distance,$value->open_base_price,$value->open_price_per_distance,$value->open_booking_base_fare,$value->open_booking_base_per_kilometer,$outofzonefee);
                    $zonePrice->base_price = $value->open_base_price;
                    $zonePrice->free_waiting_time = $value->open_free_waiting_time;
                    $zonePrice->waiting_charge = $value->open_waiting_charge;
                    $zonePrice->price_per_time = $value->open_price_per_time;
                    $zonePrice->base_distance = $value->open_base_distance;
                    $zonePrice->price_per_distance = $value->open_price_per_distance;
                    $zonePrice->booking_base_fare = $value->open_booking_base_fare;
                    $zonePrice->booking_base_per_kilometer = $value->open_booking_base_per_kilometer; 
                }
                
                $total_amount = $totalvalue['sub_total'];

                // set surge price
                foreach($value->getSurgePrice as $key1 => $value1){
                    if($value1->start_time <= date("H:i",strtotime($data['ride_time'])) && $value1->end_time >= date("H:i",strtotime($data['ride_time'])) && in_array(date("l",strtotime($data['ride_date'])),explode(',',$value1->available_days))){
                        if($data['destination_type'] == 'NORMAL' && $distance > $zonePrice->base_distance){
                            $zonePrice->computed_price = number_format($value1->surge_distance_price * ($distance - $zonePrice->base_distance),2);
                            $zonePrice->computed_distance = round($distance - $zonePrice->base_distance,2);
                            $total_amount = ($value1->surge_distance_price * ($distance - $zonePrice->base_distance)) + $value1->surge_price + $totalvalue['booking_fee'];
                        }
                        else{
                            $total_amount = $value1->surge_price;
                        }
                        
                        $zonePrice->base_price = $value1->surge_price;
                        $final_distance =  $distance - $zonePrice->base_distance;
                        $zonePrice->price_per_distance = $value1->surge_distance_price;
                        $zonePrice->distance = $distance;
                        $zonePrice->total_amount = (float)$total_amount;
                    }
                }

                $zonePrice->promo_total_amount = number_format($total_amount,2);
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
                    // dd($user->id,explode(',',$expired->user_id));
                    if($expired->select_offer_option == 5 && !in_array($request['user_id'],explode(',',$expired->user_id))){
                        return $this->sendError('Invalid Prome code',[],404);
                    }
                    if ($expired->promo_code == $request['promo_code']) {    
                        // if(in_array($value->getType->id,$expired->types)){
                            // dump($value->getType->vehicle_name);
                            // dump($total_amount,$expired->target_amount);
                            // dump($total_amount > $expired->target_amount);
                            if($total_amount > $expired->target_amount){
                                $zonePrice->promo_code = 1;

                                $total_amounts = $this->promoCalculation($expired,$total_amount);

                                $zonePrice->promo_total_amount = (double)round($total_amounts,2);
                                $total_amounts = str_replace(',', '', $total_amounts);
                                $amounts = (double) $total_amount - (double) $total_amounts;
                                $zonePrice->promo_amount = (double)round($amounts,2);
                                $zonePrice->promo_msg = "";
                            }
                            else{
                                $zonePrice->promo_code = 1;
                                $zonePrice->promo_msg = "Sorry, this promo not apply";
                            }
                        // }
                        // else{
                        //     $zonePrice->promo_code = 1;
                        //     $zonePrice->promo_msg = "Sorry, this promo not apply";
                        // }
                    } 
                    else  
                    {
                        $zonePrice->promo_code = 0;
                    }
                }
                    $zonePrice->distance = $distance;

                  //  $total_amount = number_format($total_amount,2);
                    // $num =   explode('.', $total_amount);
                    // if(count($num) > 1){
    
                    //     if ($num[1] < 10) {
                    //         $num[1] = $num[1] . '0';
                    //     }
                    //     $num[1] = intval($num[1]);
    
                    //     if ($num[1] >= 0 && $num[1] < 26) {
                    //         $num[1] = 00;
                    //     } elseif ($num[1] >= 26 && $num[1] <= 75) {
                    //         $num[1] = 50;
                    //     } else {
                    //         $num[0] = $num[0] + 1;
                    //         $num[1] = 0;
                    //     }
                    //    $total_amount =  $num[0] . '.' . $num[1];
                    // }

                    $calculated_trip_price = (int) $total_amount;
                    $remainder = fmod($calculated_trip_price, 10);
                    $quotient = $calculated_trip_price - $remainder;
                
                    $amount_to_add = 0;
                    if ($remainder >= 0 && $remainder < 2.6) {
                        $amount_to_add = 00;
                    } elseif ($remainder >= 2.6 && $remainder <= 7.5) {
                        $amount_to_add = 5;
                    } else {
                        $amount_to_add = 10;
                    }
                    $total_amount = $quotient + $amount_to_add;

                    

                    $zonePrice->total_amount = (float)$total_amount;
                    $zonePrice->booking_fees = $totalvalue['booking_fee'];
                    $zonePrice->outofzone = $totalvalue['outofzonefee'];

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

       // dd("hai");
        $reqs = [ 
            'trip_type' => 'required',
            'customer_number' => 'required',
            'customer_name' => 'required',
            'trip_types' => 'required',
            'pickup_lat' => 'required',
            'pickup_lng' => 'required',
            // 'customer_slug' => 'required',
            'destination_type' => 'required',
            'pickup' => 'required',
            'trip_amount' => 'required',
            'type' => 'required'
        ];

        if($request->destination_type == 'NORMAL'){
            $reqs['drop_lat'] = 'required';
            $reqs['drop_lng'] = 'required';
            $reqs['drop'] = 'required';
        }
        $validator = Validator::make($request->all(), $reqs);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('Dispatcher approval')) {
            return $this->sendError('Unauthorized',[],401);    
        }
        // dd($request->all());
        $create_id = NULL;
        if(!$user->hasRole("Super Admin")){
            $create_id = $user->id;
        }
        if($request->ride_date_time && $request->trip_type == "RIDE_LATER"){
            $one_hour = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');
            // dump($one_hour);
            // dd(date("Y-m-d H:i:s",strtotime($request->ride_date_time)));
            if(date("Y-m-d H:i:s",strtotime($request->ride_date_time)) < $one_hour){
                return $this->sendError('Set time the after 30 minutes for current time',[],403);
            }
        }

        $user = User::where('phone_number',$request->customer_number)->role('user')->first();

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

        $zone = $this->getZone($request->pickup_lat, $request->pickup_lng);  
        if (!$zone) {
            return $this->sendError('Service not available at this location',[],403);
        }

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
            return (new DispatcherRideLaterController())->rideLater($request,$zone,$user_details,$zone_type_id,$create_id);
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
            if($promocode->select_offer_option == 5 && !in_array($user->id,explode(',',$promocode->user_id))){
                return $this->sendError('Invalid Prome code',[],404);
            }
            // if(!in_array($type->id,$promocode->types)){
            //     return $this->sendError('Invalid Prome code',[],404);
            // }

            $promo_all_count = $this->request->where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count >= $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }
        // dd($promocode_id);


        if ($request->has('pickup_lat')) {

            $check_rider_address = RiderAddress::where('title',$request->pickup)->get();
            if(count($check_rider_address) == 0){
                RiderAddress::create([
                    'title' => $request->pickup,
                    'latitude' => $request->pickup_lat,
                    'longitude' => $request->pickup_lng,
                    'riderId' => 1,
                ]);
            }
        }


        if ($request->has('drop_lat')) {

            $check_rider_address = RiderAddress::where('title',$request->drop)->get();
            if(count($check_rider_address) == 0){
                RiderAddress::create([
                    'title' => $request->drop,
                    'latitude' => $request->drop_lat,
                    'longitude' => $request->drop_lng,
                    'riderId' => 1,
                ]);
            }
        }


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
                'base_price'       => $request->has('base_price') ? $request->base_price : 0,
                'amount' => $request->trip_amount,
                'distance_cost' => $request->computed_price,
                'vehicle_type' => $request->type,
            ];

            // dd($request_params);
                    // @ TODO The trip amount deducted to user wallet.
            $user_wallet = Wallet::where('user_id',$user->id)->first();
            
            if($request->has('drop') && $request->has('drop_lat') && $request->has('drop_lng')){
                $tripAmount = $request->base_price;
            }else{
                $tripAmount = $request->trip_amount;
            }
            $request_params['wallet_deduct_amount'] = NULL;
            if($user_wallet){
                $user_deduct_amount = Settings::where('name','trip_wallet_deduct_amount')->first();
                $wallet_deduct_amount = $user_deduct_amount ? $user_deduct_amount->value :50;  // static value 
               

                if($tripAmount > $wallet_deduct_amount && $user_wallet->balance_amount >= $wallet_deduct_amount ){
                    $request_params['wallet_deduct_amount'] = $wallet_deduct_amount;
                }elseif($user_wallet->balance_amount <= $wallet_deduct_amount){
                    $request_params['wallet_deduct_amount'] = $user_wallet->balance_amount;  
                }elseif($tripAmount < $user_wallet->balance_amount ){
                    $request_params['wallet_deduct_amount'] = $tripAmount;
                }

            }

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
                'poly_string' => $request->poly_string,
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
            $selected_drivers = [];
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
        //    dd($drivers);
            if ($drivers->success == true) {
                
                $noval =0;
                foreach ($drivers->data as $key => $value) {
                    // dd($value);
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
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
                    // $selected_drivers[$key] = $drivers_list;
                    array_push($selected_drivers,$drivers_list);

                     //dd($selected_drivers[$key]);
                }
               
            }
            else{
                $request_detail->is_cancelled = true;
                $request_detail->cancelled_at = NOW();
                $request_detail->cancel_method = 'Automatic';
                $request_detail->save();
    
                return $this->sendError('No Driver Found',$request_detail,404);  
            }
            $data = new \stdCLass();
            $data->result = $request_detail;
            if((object) $selected_drivers) {
                $data->drivers = (array)$selected_drivers;
            }else {
                $data->drivers = (array)$selected_drivers;

            }
            return $this->sendResponse('Data Found', $data, 200);
        }
           
        $selected_drivers = [];
        $result = fractal($request_detail, new TripRequestTransformer);

        $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);

      //  dd($drivers);
        
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
                $request_detail->is_cancelled = true;
                $request_detail->cancelled_at = NOW();
                $request_detail->cancel_method = 'Automatic';
                $request_detail->save();
                return $this->sendError('No Driver Found',$request_detail,404);  
            }
        }else{
            $request_detail->is_cancelled = true;
            $request_detail->cancelled_at = NOW();
            $request_detail->cancel_method = 'Automatic';
            $request_detail->save();

            return $this->sendError('No Driver Found',$request_detail,404);  
        }

        
        foreach ($selected_drivers as $key => $selected_driver) {
            $metaDriver = User::where('id',$selected_driver['driver_id'])->first();
            $wallet = Wallet::where('user_id',$selected_driver['driver_id'])->where('balance_amount','>',settingValue('wallet_driver_minimum_balance_for_trip'))->first();
            if($metaDriver && $wallet){
            
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
                // dispatch(new SendPushNotification($title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1,$sub_title));
                sendPush($title, $sub_title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1);

                $request_meta = $request_detail->requestMeta()->create($selected_driver);

                $request_meta = $request_detail->requestDedicatedDrivers()->create($selected_driver);
            }
        }
        if($request_detail->requestMeta()->count() == 0){
            return $this->sendError('No Driver Found',$request_detail,404);
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

    public function dispatchRequestView(Request $request,$ride)
    {

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('Dispatcher approval')) {
            return $this->sendError('Unauthorized',[],401);    
        }
        $request = $this->request->where('id',$ride)->first();
        $outstation_trip = OutstationUploadImages::where('request_id',$ride)->first();
        $types = Vehicle::where('status',1)->get();
        $outstations = OutstationPriceFixing::where('status',1)->get();
        $result = fractal($request, new TripRequestTransformer)->parseIncludes('requestBill');
        return $this->sendResponse('Data Found', $result, 200);
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

    public function dispatcherTripList(Request $request,$status)
    {

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('Dispatcher approval')) {
            return $this->sendError('Unauthorized',[],401);    
        }
        $requests_now = RequestModel::where('if_dispatch',1)->where('trip_type','LOCAL')->orderby('created_at','desc');
        // $requests_now = RequestModel::where('if_dispatch',1)->where('trip_type','LOCAL')->whereNotNull('driver_id')->orderby('created_at','desc');

        if($status == 'completed'){
            $requests_now = $requests_now->where('is_completed',1);
        }
        else if($status == 'cancelled'){
            $requests_now = $requests_now->where('is_cancelled',1);
        }
        else if($status == 'on_going'){
            $requests_now = $requests_now->where('is_completed',0)->where('is_cancelled',0)->where('is_driver_started',1);
        }
        else if($status == 'upcomming'){
            $requests_now = $requests_now->where('is_completed',0)->where('is_cancelled',0)->where('is_driver_arrived',0)->where('is_driver_started',0);
        }

        $requests_now = $requests_now->paginate(10);

	foreach ($requests_now as $key => $requestlist)
        {
            $requests_now[$key] =  fractal($requestlist, new TripRequestTransformer);
        }
        $result = new \stdClass();
        $result->history = $requests_now;

        return $this->sendResponse('Data Found', $requests_now, 200);
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

        dispatch(new SendPushNotification($title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1,$sub_title));

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

    public function assignDriver(Request $request)
    {

        $req = [ 
            'ride_id' => 'required',
            'driver_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $req);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        $ride = $request->ride_id;
        $driver = $request->driver_id;
        $request_detail = RequestModel::where('id',$ride)->first();

        $message = 'trip_accepted';

        $updated_params = [
            'driver_id'         => $driver,
            // 'accepted_at'       => now(),
            // 'is_driver_started' => 1,
            'hold_status'       => 1,
           // 'created_by'        => $request_detail->user_id
        ];

        $request_detail->update($updated_params);
        
        RequestMeta::where('request_id', $ride)->delete();
        // dd($request_detail->user_id);
        // send otp user details
        $userDetails = User::find($request_detail->user_id);

        $driverDetails = User::where('id',$driver)->first();

        $car_details = Driver::where('user_id',$driver)->first();

        $request_result =  fractal($request_detail, new TripRequestTransformer);
        
        if($request_detail->is_later == 0){

            $driver_details = Driver::where('user_id',$driver)->first();
                
            // Update the driver's available state as false
            $driver_details->is_available = false;
            $driver_details->total_accept += 1;
            $driver_details->reject_count = 0;
            $driver_details->save();

                    
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

                dispatch(new SendPushNotification($title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1,$sub_title));
            }
        }
        $otp = $this->UniqueRandomNumbers(4);
        $otp_update = RequestModel::where('id',$ride)->first();
            
        $updated_params_otp = [
            'request_otp'         => $otp,
        ];
            
        $otp_update->update($updated_params_otp);



        return $this->sendResponse('Driver Assign Successfully', $request_result, 200);
    }

    public function dispatchTripCancel(Request $request)
    {

        $req = [ 
            'ride_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $req);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $ride = $request->ride_id;

        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        $request_detail = RequestModel::where('id',$ride)->first();
        if(!$request_detail || $request_detail->is_cancelled == 1){
            return $this->sendError('Request not found',[],401);
        }

        $request_detail->cancelled_at = NOW();
        $request_detail->is_cancelled = 1;
        $request_detail->cancel_method = CancelMethod::DISPATCHER_TEXT;
        $request_detail->hold_status = 0;
        $request_detail->comments = $request->comments;
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

            // dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));
            sendPush($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0);
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

            // dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));
            sendPush($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0);
        }

        return $this->sendResponse('Trip Cancelled Successfully', $request_result, 200);
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

        if($request->manual_trip == "MANUAL"){
//dd("dai");          
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
                    // $selected_drivers[$key] = $drivers_list;
                    array_push($selected_drivers,$drivers_list);
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

        dispatch(new SendPushNotification($title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1,$sub_title));

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
            
            dispatch(new SendPushNotification($title,$pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));
        }
        return $this->sendResponse('Trip Cancelled', $request_detail, 200);
    }

    public function ClosedTrip(Request $request)
    {
        $data = $request->all();

        $request_detail = RequestModel::where('id',$data['request_id'])->first();
        
        $trip_id = $request_detail->id;
        
        
        if($request_detail->destination_type == "OPEN") {
        
            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
                'amount' => 'required',
            ]);
        }else {
               $validator = Validator::make($request->all(), [
                'request_id' => 'required',
            ]);
        }
      
        if($validator->fails()){
            return $this->sendError('Validation Error',$validator->errors(),412);       
        }
        

        if(is_null($request_detail)){
            return $this->sendError('Request not found',[],404);
        }

        if($request_detail->is_trip_start == 0){
            return $this->sendError('Driver not started the trip',[],404);
        }

        if($request_detail->is_cancelled == 1 ||  $request_detail->is_completed == 1){
            return $this->sendError('Already this trip closed',[],404);
        }
        
        
         $userModel = User::find($request_detail->driver_id);
         
         try {
        
           $client = new Client([
            'base_uri' =>
                env('NODE_GEOFIRE_URL', 'http://localhost') .
                ':' .
                env('NODE_GEOFIRE_PORT', 4000),
        ]);

        $url = "request/{$trip_id}";
        
        
         $result = $client->get($url, [
            'timeout' => 15,
            'connect_timeout' => 5,
        ]);
        
      
      
        if ($result->getStatusCode() == 200) {
            $data = json_decode($result->getBody()->getContents());
            
          
            $drop_lat = $data->data->lat;
            $drop_lng = $data->data->lng;
            $triprequest_id = $data->data->request_id;
            $distancee = $data->data->distancee;
            $id = $data->data->id;
            $waiting_time = $data->data->waiting_time;
            $user_id = $data->data->user_id;
            
             if($request_detail->destination_type == "OPEN") {
                $location = $data->data->lat . ',' . $data->data->lng;
                
            if ($data->data->lat == '0.0' && $data->data->lng == '0.0') {
                return $this->sendError('Reverse Geocode Failed...', [], 404);
            } else {
          
                $request_result = $request_detail->update(['closed_trip_amount' => $request->amount]);
                $drop_Address   = $this->reverse($location);
            }
            }else {
                 $drop_Address = $data->data->drop_Address;
            }
           
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'http://85.214.68.2/taxi/public/api/V1/request/dispatcher/end');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "drop_lat=$drop_lat&drop_lng=$drop_lng&request_id=$triprequest_id&distance=$distancee&waiting_time=$waiting_time&drop_address=$drop_Address");

            $headers = array();
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            
        }

    } catch (\Throwable $th) {
      
        return response()->json(['success' => false,'data' => 'No driver found']);
    }
        
        
        return $this->sendResponse('Dispatcher requested to close the trip', $request_detail, 200);
    }
}


