<?php

namespace App\Console\Commands;

use App\Constants\PushEnum;
use App\Jobs\Request\NoDriverFoundNotifyJob;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\Request;
use App\Models\taxi\ZonePrice;
use App\Models\User;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\Requests\NoDriverTrips;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\OutstationMaster;
use Illuminate\Console\Command;
use App\Traits\CommanFunctions;
use Illuminate\Support\Carbon;
use Log;
use App\Models\taxi\DriverLogs;


class AssignDriversForScheduledRides extends Command
{

    use CommanFunctions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign_drivers:for_schedule_rides';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Drivers for schdeulesd rides';

    /**
     * Create a new command instance.
     *
     * @return void
     */
   
    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $prescheduleTime = 15;
        $current_date = now()->format('Y-m-d H:i:s');
        $add_45_min = now()->addMinutes($prescheduleTime)->format('Y-m-d H:i:s');
     
        $requests = Request::where('is_later', 1)
                    ->where('trip_start_time', '<=', $add_45_min)
                    ->where('trip_start_time', '>', $current_date)
                    ->where('is_completed', 0)->where('is_cancelled', 0)->where('is_driver_started', 0)->join('request_places', 'requests.id', '=', 'request_places.request_id')->get(['request_places.pick_lat','request_places.pick_lng','request_places.drop_lat','request_places.drop_lng','request_places.pick_address','request_places.drop_address','request_places.created_at','request_places.updated_at','requests.zone_type_id','requests.id','requests.package_item_id','requests.trip_type','requests.outstation_id','requests.outstation_type_id']);
       
        foreach ($requests as $key => $request) {
        
            // Check if the request has any meta drivers
            if ($request->requestMeta()->exists()) {
                break;
            }
            $place = $request->requestPlace();

            // dd($request->pick_lng);
         
            // fetch the Type Slug 
            $type_slug = "";
            $type = ZonePrice::where('id',$request->zone_type_id)->with(['getType'])->first();
            if($type){
                $type_slug = $type->getType->slug;
            }
            $package_item = PackageItem::where('id',$request->package_item_id)->where('status',1)->first();
            // dd($request->package_item_id);
            if($package_item){
                $type_slug = $package_item->getVehicle->slug;
            }
            $outstation_item = OutstationPriceFixing::where('id',$request->outstation_type_id)->where('status',1)->first();
            // dd($request->package_item_id);
            if($outstation_item){
                $type_slug = $outstation_item->getVehicle->slug;
            }

            // dump($type_slug);
            if($type_slug){
                // dump($request->pick_lat,$request->pick_lng,$type_slug, $request->trip_type);
                $drivers = fetchDrivers($request->pick_lat,$request->pick_lng,$type_slug, $request->trip_type);
                // dd($drivers);
                $drivers = json_decode($drivers->getContent());

                if ($drivers->success == false) {
                    $no_driver_request_ids = [];
                    $no_driver_request_ids[0] = $request->id;
                    dispatch(new NoDriverFoundNotifyJob($no_driver_request_ids));
                }else{
                    $selected_drivers = [];
                    $request_detail = Request::where('id',$request->id)->first();

                    // dd($request_detail);
                    foreach ($drivers->data as $key => $driver) {
                        $driverdet = User::where('slug',$driver->id)->first();
                        $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                        if($driverdet->active && $metta == 0){
                            $selected_drivers[$key]["user_id"] = $request_detail->user_id;
                            $selected_drivers[$key]["driver_id"] = $driverdet->id;
                            $selected_drivers[$key]["active"] = ($key == 0 ? 1 : 0);
                            $selected_drivers[$key]["request_id"] = $request_detail->id;
                            $selected_drivers[$key]["assign_method"] = 1;
                            $selected_drivers[$key]["created_at"] = date('Y-m-d H:i:s');
                            $selected_drivers[$key]["updated_at"] = date('Y-m-d H:i:s');
                        }
                    }
                    // dd($selected_drivers);
                    // if(count($selected_drivers) == 0){
                    //     return $this->sendError('No Driver Found',[],404);  
                    // }
                    
                    $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
                
                    

                    $result = fractal($request_detail, new TripRequestTransformer);

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

                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::REQUEST_CREATED;
                    $socket_data->result = $result;

                    $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];

                    dispatch(new SendPushNotification($title, $sub_title, $pushData, $metaDriver->device_info_hash, $metaDriver->mobile_application_type,0));

                    foreach ($selected_drivers as $key => $selected_driver) {
                        $request_meta = $request_detail->requestMeta()->create($selected_driver);
                    }
                }
            }
        }

        $add_15_min = now()->addMinutes(15)->format('Y-m-d H:i:s');
        $requests1 = Request::where('trip_start_time', '<=', $add_15_min)
                    ->where('trip_start_time', '>', $current_date)
                    ->where('is_completed', 0)->where('is_cancelled', 0)->where('hold_status',1)->get();
        // dump($current_date);
        // dump($add_45_min);
        // dd($requests1);
        foreach ($requests1 as $key => $request) {
            // Check if the request has any meta drivers
            if ($request->requestMeta()->exists()) {
                break;
            }
            $place = $request->requestPlace();

            // dd($request->pick_lng);
            $request_detail = Request::where('id',$request->id)->first();
         
            // fetch the Type Slug 
            // $type = ZonePrice::where('id',$request->zone_type_id)->with(['getType'])->first();
            // $type_slug = $type->getType->slug;
                
            $metaDriver = User::where('id',$request_detail->driver_id)->first();
            // dd($metaDriver);
            $result = fractal($request_detail, new TripRequestTransformer);

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
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CREATED;
            $socket_data->result = $result;
            $socketData = ['event' => 'request_'.$metaDriver->slug,'message' => $socket_data];
            sendSocketData($socketData);

            $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
            dispatch(new SendPushNotification($title, $sub_title, $pushData, $metaDriver->device_info_hash,$metaDriver->mobile_application_type,0));
            // dd($pushData);
        }

        $request2 = Request::where('is_driver_started',1)->where('is_driver_arrived',0)->where('is_cancelled',0)->get();

        foreach ($request2 as $key => $value) {
            $one_hour = Carbon::parse($value->trip_start_time)->addHours(3);
            if(Carbon::now() >= $one_hour){
                $value->update([
                    'cancelled_at' => NOW(),
                    'is_cancelled' => 1,
                    'cancel_method' => 'Automatic'
                ]);
                $driver = User::where('id',$value->driver_id)->first();
                $user = User::where('id',$value->user_id)->first();

                if ($driver) {
                    $driver->driver->is_available = true;
                    $driver->driver->save();
                    $title = Null;
                    $body = '';
                    $lang = $driver->language;
                    $push_data = $this->pushlanguage($lang,'trip-cancel');
                    if(is_null($push_data)){
                    $title = 'Trip Cancelled By Customer';
                    $body = 'Driver not arrived so the trip cancelled';
                    $sub_title = 'Driver not arrived so the trip cancelled';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    } 
                    $request_result =  fractal($value, new TripRequestTransformer);
                    // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => (string)$request_result->toJson()];
                    $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => $request_result];
                    
                    $notifiable_driver = $driver;

                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_USER;
                    $socket_data->result = $request_result;
                    
                    $socketData = ['event' => 'request_'.$notifiable_driver->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    dispatch(new SendPushNotification($title, $sub_title, $sub_title, $pushData, $notifiable_driver->device_info_hash, $notifiable_driver->mobile_application_type,0));
                }

                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'trip-cancel');
                    if(is_null($push_data)){
                    $title = 'Trip Cancelled By Customer';
                    $body = 'Driver not arrived so the trip cancelled';
                    $sub_title = 'Driver not arrived so the trip cancelled';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    } 
                    $request_result =  fractal($value, new TripRequestTransformer);
                    // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => (string)$request_result->toJson()];
                    $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => $request_result];
                    
                    $notifiable_driver = $user;

                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_USER;
                    $socket_data->result = $request_result;
                    
                    $socketData = ['event' => 'request_'.$notifiable_driver->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    dispatch(new SendPushNotification($title,$sub_title,$pushData, $notifiable_driver->device_info_hash, $notifiable_driver->mobile_application_type,0));
                }
            }
        }

        $request3 = Request::where('is_driver_started',0)->where('is_driver_arrived',0)->where('is_cancelled',0)->get();

        foreach ($request3 as $key => $value) {
            // dump(NOW());
            // dump($value->trip_start_time);
                Log::Info(Carbon::now());
                Log::Info(Carbon::parse($value->trip_start_time)->addMinutes(10));
                // dd(Carbon::now() > Carbon::parse($value->trip_start_time)->addMinutes(5));
            if(Carbon::now() > Carbon::parse($value->trip_start_time)->addMinutes(10)){
                // dd($value);
                $value->update([
                    'cancelled_at' => NOW(),
                    'is_cancelled' => 1,
                    'cancel_method' => 'Automatic'
                ]);
                $user = User::where('id',$value->user_id)->first();
                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'trip-cancel');
                    if(is_null($push_data)){
                    $title = 'Trip Cancelled By Customer';
                    $body = 'Driver not arrived so the trip cancelled';
                    $sub_title = 'Driver not arrived so the trip cancelled';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    } 
                    $request_result =  fractal($value, new TripRequestTransformer);
                
                    // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => (string)$request_result->toJson()];
                    $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => $request_result];
                    
                    $notifiable_driver = $user;

                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_USER;
                    $socket_data->result = $request_result;
                    
                    $socketData = ['event' => 'request_'.$notifiable_driver->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    dispatch(new SendPushNotification($title,$sub_title, $pushData, $notifiable_driver->device_info_hash, $notifiable_driver->mobile_application_type,0));
                }
            }
        }

        $request_delete = Request::where('is_driver_started',0)->where('is_driver_arrived',0)->where('is_cancelled',1)->whereNull('driver_id')->get();
        foreach ($request_delete as $key => $value) {
            NoDriverTrips::create([
                'user_id' => $value->user_id,
                'pick_up' => $value->requestPlace->pick_address,
                'drop' => $value->requestPlace->drop_address,
                'datetime' => date("Y-m-d H:i:s",strtotime($value->trip_start_time)),
                'trip_type' => $value->trip_type
            ]);
            $value->delete();
        }
        // Request::where('is_driver_started',0)->where('is_driver_arrived',0)->where('is_cancelled',1)->whereNull('driver_id')->delete();

        $drivers = fetchDriversNotUpdated(11.0116775,76.8271451);
        $drivers = json_decode($drivers->getContent());
        // dd($drivers);

        if ($drivers->success == false) {
            // $no_driver_request_ids = [];
            // $no_driver_request_ids[0] = $request->id;
            // dispatch(new NoDriverFoundNotifyJob($no_driver_request_ids));
        }else{
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->where('online_by',1)->first();
                if(!is_null($driverdet)){
                    if($driverdet->mobile_application_type == "ANDROID"){
                        $title = Null;
                        $body = '';
                        $sub_title = '';
                        $lang = $driverdet->language;
                        $push_data = $this->pushlanguage($lang,'silent-push');
                        if(is_null($push_data)){
                            $title = 'Roda Raja You Are Online';
                            $body = 'Silent Push';
                            $sub_title = 'Silent Push';
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title =  $push_data->description;
                        }   
                        $pushData = ['notification_enum' => PushEnum::SILENT_PUSH];
                        dispatch(new SendPushNotification($title, $sub_title, $pushData, $driverdet->device_info_hash, $driverdet->mobile_application_type,0));
                    }
                }
            }
        }

        

        $drivers = fetchDriversLogout(11.0116775,76.8271451);
        $drivers = json_decode($drivers->getContent());
        // dd($drivers);

        if ($drivers->success == false) {
            // $no_driver_request_ids = [];
            // $no_driver_request_ids[0] = $request->id;
            // dispatch(new NoDriverFoundNotifyJob($no_driver_request_ids));
        }else{
            foreach ($drivers->data->data as $key => $driver) {
                $driverdet = User::where('slug',$driver->id)->first();
                if(!is_null($driverdet)){
                    if($driverdet->mobile_application_type == "ANDROID"){
                        $driverdet->online_by = 0;
                        $driverdet->save();

                        $logs = DriverLogs::create([
                            'driver_id' => $driverdet->id,
                            'date' => date('Y-m-d'),
                            'offline_time' => NOW(),
                            'status' => 1
                        ]);

                        $title = Null;
                        $body = '';
                        $sub_title = '';
                        $lang = $driverdet->language;
                        $push_data = $this->pushlanguage($lang,'logout-push');
                        if(is_null($push_data)){
                            $title = 'Logout Push 😊️';
                            $body = 'Logout Push';
                            $sub_title = 'Logout Push';
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title =  $push_data->description;
                        }   
                        $pushData = ['notification_enum' => PushEnum::LOGOUT_PUSH];
                        dispatch(new SendPushNotification($title, $sub_title, $pushData, $driverdet->device_info_hash, $driverdet->mobile_application_type,0));
                    }
                }
            }
        }
    }
}
