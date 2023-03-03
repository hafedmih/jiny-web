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
use App\Traits\RandomHelper;
use Illuminate\Support\Facades\Http;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\Requests\RequestSetAmount;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Customer;
use App\Models\taxi\Promocode;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Zone;
use App\Models\taxi\Driver;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\OutstationUploadImages;
use App\Models\User;
use App\Models\boilerplate\OauthClients;

use Illuminate\Support\Carbon;

class CreateDispatcherRequestController extends BaseController
{
    use CommanFunctions,RandomHelper;

    public $request;

    public function __construct(RequestModel $request) {
        
        $this->request = $request;
    } 

    public function dispatcher(Request $request)
    {
        $package_detail = PackageMaster::where('status',1)->get();

        $outstanding_pickup = OutstationMaster::where('status',1)->groupby('pick_up')->get();
        $outstanding_drops = OutstationMaster::where('status',1)->groupby('drop')->get();
        $package_list = PackageMaster::where('status',1)->get();
        $outstation_price = OutstationPriceFixing::where('status',1)->get();

        return view('taxi.createdispatcher.CreateDispatcherRequest',['package_detail' => $package_detail,'outstanding_pickup' => $outstanding_pickup,'outstanding_drops' => $outstanding_drops,'package_list' => $package_list]);
    }

    public function getVehicleTypes(Request $request)
    {
        $data = $request->all();

        if($data['pickup_lat'] == '' && $data['pickup_long'] == ''){
            return null;
        }
        
        $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);

        if(is_null($zone))
            return $this->sendError('Non services zone',[],404);

        if($zone->non_service_zone == 'Yes'){
            return $this->sendError('Non services zone',[],404);
        }

        if($data['category'] == 'LOCAL'){
            $zone = Zone::where('id',$zone->id)->with('getZonePrice','getZonePrice.getType')->first();
        }
        elseif($data['category'] == 'RENTAL'){
            $zone = PackageItem::with('getVehicle')->where('status',1)->where('package_id',$data['rental_id'])->get();
        }
        elseif($data['category'] == 'OUTSTATION'){
            $zone = OutstationPriceFixing::with('getVehicle')->where('status',1)->get();
        }

        return $this->sendResponse('Data Found',$zone,200); 
    }

    public function getVehicleDrivers(Request $request)
    {
        $data = $request->all();
        $selected_drivers = array();
        // dump($request->pickup_lat,$request->pickup_lng,$request->types, $request->category);
        $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->types, $request->category);
        // dd($drivers);
        $drivers = json_decode($drivers->getContent());
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
                // dump($drivers_list);
                $selected_drivers[$key] = $drivers_list;
                return $this->sendResponse('Data Found',$selected_drivers,200); 
            }           
        }
        else{
            return $this->sendError('No Driver Found',[],404);  
        }
    }

    public function createDispatchRequestSetAmount(Request $request)
    {
        $data = $request->all();
        // dd($data);

        if($request->ride_date_time && $request->trip_type == "RIDE_LATER"){
            $one_hour = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');
            // dump($one_hour);
            // dd(date("Y-m-d H:i:s",strtotime($request->ride_date_time)));
            if(date("Y-m-d H:i:s",strtotime($request->ride_date_time)) < $one_hour){
                return $this->sendError('Set time the after 30 minutes for current time',[],403);
            }
        }

        $user = User::where('slug',$request->customer_slug)->orWhere('phone_number',$request['customer_number'])->first();

        if(!$user){
            $user = User::create([
                'firstname' => $request['customer_name'],
                'phone_number' => $request['customer_number'],
                // 'address' => $request['customer_address'],
            ]);
        }

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

        //driver check
        $driver_id = User::where('id',$request->driver_id)->role('driver')->first();
        if(!$driver_id){
            return $this->sendError('Invalid Driver',[],403);
        }

        // find zone
        if(!$request->has('types')){
            return $this->sendError('Vechile Type is Required',[],403);
        }
        $type = Vehicle::where('slug',$request->types)->first();
        if(is_null($type)){
            return $this->sendError('wrong Vechile Type',[],403);
        }
        $package_item = '';
        if($data['category'] == "RENTAL"){
            $package = PackageMaster::where('id',$data['rental_id'])->first();
            if(is_null($package)){
                return $this->sendError('Wrong Package',[],403);
            }
            $package_item = PackageItem::with('getVehicle')->where('status',1)->where('package_id',$data['rental_id'])->where('type_id',$type->id)->first();
        }
        $outstation_item = '';
        $outstation = '';
        if($data['category'] == "OUTSTATION"){
            $outstation = OutstationMaster::where('drop',$data['outstation_id'])->first();
            if(is_null($outstation)){
                return $this->sendError('Wrong Outstation',[],403);
            }
            $outstation_item = OutstationPriceFixing::with('getVehicle')->where('status',1)->where('type_id',$type->id)->first();
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

        $requestNumber = generateRequestNumber();
        $request_params = [
            'request_number'          => $requestNumber,
            'if_dispatch'             => true,
            'request_otp'             => 1234, //rand(1111, 9999),
            'user_id'                 => $user->id,
            'driver_id'                 => $request->driver_id,
            'zone_type_id'            => $zone_type_id,
            'payment_opt'             => "Cash",
            'requested_currency_code' => $zone->getCountry->currency_code,
            'requested_currency_symbol' => $zone->getCountry->currency_symbol,
            'trip_type'                 => $request->category,
            'manual_trip'             => 'MANUAL',
            'trip_start_time'         => $request->trip_type == "RIDE_LATER" ? $request->ride_date_time : NOW(),
            'package_id'              => $data['rental_id'],
            'package_item_id'         => $package_item ? $package_item->id : NULL,
            'outstation_id'           => $outstation ? $outstation->id : NULL,
            'outstation_type_id'      => $outstation_item ? $outstation_item->id : NULL,
            'driver_notes'            => $request->driver_notes,
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

        RequestSetAmount::create([
            'request_id' => $request_detail->id,
            'request_amount' => $request['assign_amount'],
            'amount_per_km' => $request['assign_amount_km'],
            'status' => 1
        ]);
 
        $result = fractal($request_detail, new TripRequestTransformer);
        if($request->trip_type == "RIDE_NOW"){
            $metaDriver = User::where('id',$request->driver_id)->first();
            
            $title = 'New Trip Requested 😊️';
            $body = 'New Trip Requested, you can accept or Reject the request';
            $sub_title = 'New Trip Requested, you can accept or Reject the request';
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CREATED;
            $socket_data->result = $result;
            $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
            dispatch(new SendPushNotification($title, $sub_title,$pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,1));
        }

        return $this->sendResponse('Request Created Successfully', $request_detail, 200);
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
   
}



