<?php

namespace App\Http\Controllers\Taxi\Web\Dispatcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Taxi\API\Request\CreateTripRequest;
use App\Traits\CommanFunctions;
use Illuminate\Http\Request;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\Promocode;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Customer;
use App\Models\taxi\Vehicle;
use App\Models\taxi\OutstationMaster;
use App\Models\User;
use App\Models\taxi\OutstationPriceFixing;
use App\Constants\PushEnum;
use App\Jobs\SendPushNotification;
use DB;
use App\Http\Controllers\API\BaseController as BaseController;

class DispatcherRideLaterController extends BaseController
{
    use CommanFunctions;
    
    public function rideLater($request,$zone,$user,$zone_type_id,$create_id)
    {
        $type = Vehicle::where('slug',$request->type)->first();
        $promocode_id =0;
            if (request()->has('promo_code') && $request->promo_code != ""){
                
                $promocode = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
                if(is_null($promocode))
                    return $this->sendError('Wrong Promo Code',[],403);
                    
                $promocode_id = $promocode->id;

                $promo_count = RequestModel::where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
                if($promo_count > $promocode->promo_user_reuse_count)
                    return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);

                if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                    return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],404);
                }
                if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                    return $this->sendError('Invalid Prome code',[],404);
                }
                if(!in_array($type->id,$promocode->types)){
                    return $this->sendError('Invalid Prome code',[],404);
                }
                $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
                if($promo_all_count > $promocode->promo_use_count)
                    return $this->sendError('Sorry! promo code exit',[],403);
            }

        $request_detail = RequestModel::leftJoin('request_places','request_places.request_id','=','requests.id')->where('requests.if_dispatch',1)->where('requests.user_id',$user->id)->where('pick_lat',$request->pickup_lat)->where('pick_lng',$request->pickup_lng)->where('drop_lat',$request->drop_lat)->where('drop_lng',$request->drop_lng)->where('requests.is_later',1)->where('requests.manual_trip',$request->manual_trip)->where('requests.is_trip_start',0)->where('requests.is_driver_started',0)->where('requests.is_cancelled',0)->whereNull('requests.driver_id')->select('requests.*')->first();
        if(!$request_detail){
            $requestNumber = generateRequestNumber();

            $request_params = [
                'is_later'                => true,
                'if_dispatch'             => true,
                'ride_type'				  => 'Ride Later',
                'trip_start_time'         => $request->ride_date_time,
                'request_number'          => $requestNumber,
                'request_otp'             => 1234, // rand(1111, 9999),
                'user_id'                 => $user->id,
                'promo_id'                => $promocode_id,
                'zone_type_id'            => $zone_type_id,
                'payment_opt'             => 'Cash',    //$request->payment_opt,
                'unit'                    => $zone->unit,
                // 'promo_id'                => $request->promocode_id,
                'requested_currency_code' => $zone->getCountry->currency_code,
                'requested_currency_symbol'=> $zone->getCountry->currency_symbol,
                'trip_type'                 => $request->trip_types,
                // 'rental_package'          => $rental_package,
                'driver_notes'            => $request->driver_notes,
                'manual_trip'             => $request->manual_trip,
                'created_by'              => $create_id,
            ];
            
            $request_detail = RequestModel::create($request_params);

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
                'stop_lat'     => $request->stop_lat,
                'stop_lng'     => $request->stop_lng,
                'stop_id'  => $request->stop_lng_id,
                'stop_address' => $request->stop,
                'poly_string'  => $request->poly_string,
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
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number,
                // 'customer_address' => $request->customer_address,
                'customer_slug' => $request->customer_slug,
                'status' => 1,
            ]);
        }
        else{
            $request_detail->zone_type_id = $zone_type_id;
            $request_detail->save();
        }

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $type = Vehicle::where('slug',$request->type)->first();
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
            $drivers = json_decode($drivers->getContent());

            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->where('users.slug',$value->id)->with('driver','driver.vehicletype')->first();
                    $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                    $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                    $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                    $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                    $distance = $value->distance / 1000 / 50;
                    $time = (int)$distance * 60;
                    if($time == 0){
                        $time = 3;
                    }
                    $hours = $time / 60;
                    $minite = $time % 60;

                    $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                    $selected_drivers[$noval] = $drivers_list;
                    $noval++;
                }
            }
            // dd($selected_drivers);
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }
        
        // $result = fractal($request_detail, new TripRequestTransformer);

        return $this->sendResponse('Request Trip Created Succassfully...', $request_detail, 200);
    }

    public function editRideLater($request,$zone,$user,$zone_type_id,$rental_package)
    {
        $requestNumber = generateRequestNumber();

        $request_detail = RequestModel::where('id',$request->ride_id)->first();

        $request_params = [
            'is_later'                => true,
            'if_dispatch'             => true,
            'ride_type'				  => 'Ride Later',
            'trip_start_time'         => $request->ride_date_time ? $request->ride_date_time : date("Y-m-d H:i:s",strtotime($request_detail->trip_start_time)),
            'request_otp'             => 1234, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'zone_type_id'            => $zone_type_id,
            'payment_opt'             => 'Cash',    //$request->payment_opt,
            'unit'                    => $zone->unit,
            // 'promo_id'                => $request->promocode_id,
            'requested_currency_code' => $zone->getCountry->currency_code,
            'requested_currency_symbol'=> $zone->getCountry->currency_symbol,
            'trip_type'                 => $request->trip_types,
            'rental_package'          => $rental_package,
            'driver_notes'            => $request->driver_notes,
            'manual_trip'             => $request->manual_trip
        ];
        if($request->manual_trip == "AUTOMATIC"){
            $request_params['driver_id'] = NULL;
        }
        
        $request_detail = $request_detail->update($request_params);
        $request_detail = RequestModel::where('id',$request->ride_id)->first();

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
            'stop_lat'     => $request->stop_lat,
            'stop_lng'     => $request->stop_lng,
            'stop_address' => $request->stop,
            'stop_id'  => $request->stop_lng_id,
            'poly_string'  => $request->poly_string,
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
            'customer_name' => $request->customer_name,
            'customer_number' => $request->customer_number,
            // 'customer_address' => $request->customer_address,
            'customer_slug' => $request->customer_slug,
            'status' => 1,
        ]);

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $type = Vehicle::where('slug',$request->type)->first();
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$request->type, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        $selected_drivers[$noval] = $drivers_list;
                        $noval++;
                    }
                }
            }
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }
        
        // $result = fractal($request_detail, new TripRequestTransformer);

        return $this->sendResponse('Request Trip Updated Succassfully...', $request_detail, 200);
    }

    public function rentalRide($request,$user,$create_id)
    {
        
        if($request->pickup == ''){
            return $this->sendError('Required pickup or drop address',[],403);
        }
        $package = PackageMaster::where('slug',$request->package)->where('status',1)->first();
        if(is_null($package)){
            return $this->sendError('Invalid Package',[],403);
        }

        $packageItem = PackageItem::where('id',$request->type)->where('status',1)->first();
        if(is_null($packageItem)){
            return $this->sendError('Invalid Package type',[],403);
        }

        if($request->trip_type == "RIDE_NOW"){
            $trip_type = "Ride Now";
            $later = false;
            $ride_date_time = NOW();
        }
        elseif($request->trip_type == "RIDE_LATER"){
            $trip_type = "Ride Later";
            $later = true;
            $ride_date_time = $request->ride_date_time;
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

            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($packageItem->type_id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }
            $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count > $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }

        $request_detail = RequestModel::leftJoin('request_places','request_places.request_id','=','requests.id')->where('requests.if_dispatch',1)->where('requests.user_id',$user->id)->where('pick_lat',$request->pickup_lat)->where('pick_lng',$request->pickup_lng)->where('requests.is_later',$later)->where('requests.manual_trip',$request->manual_trip)->where('requests.is_trip_start',0)->where('requests.is_driver_started',0)->where('requests.is_cancelled',0)->whereNull('requests.driver_id')->select('requests.*')->first();
        if(!$request_detail){
            $requestNumber = generateRequestNumber();
            $request_params = [
                'if_dispatch'             => true,
                'is_later'                => $later,
                'ride_type'				  => $trip_type,
                'trip_start_time'         => $ride_date_time,
                'request_number'          => $requestNumber,
                'request_otp'             => 1234, // rand(1111, 9999),
                'user_id'                 => $user->id,
                'package_id'              => $package->id,
                'payment_opt'             => 'Cash',   
                'promo_id'                => $promocode_id,
                'package_item_id'         => $packageItem->id,
                'trip_type'               => $request->trip_types,
                'manual_trip'             => $request->manual_trip,
                'requested_currency_code' => $package->getCountry->currency_code,
                'driver_notes'            => $request->driver_notes,
                'requested_currency_symbol'=> $package->getCountry->currency_symbol,
                'created_by'              => $create_id,
            ];
            
            $request_detail = RequestModel::create($request_params);

            $request_place_params = [
                'pick_lat'     => $request->pickup_lat,
                'pick_lng'     => $request->pickup_lng,
                // 'drop_lat'     => $outstation->drop_lat,
                // 'drop_lng'     => $outstation->drop_lng,
                'pick_address' => $request->pickup,
                'pick_up_id'   => $request->pickup_lng_id,
                // 'drop_address' => $request->drop,
                // 'stops' => $request->stop ? 1 : 0

            ];

            $request_detail->requestPlace()->create($request_place_params);

            $request_history_params = [
                'olat'         => $request->pickup_lat,
                'olng'         => $request->pickup_lng,
                // 'dlat'         => $outstation->drop_lat,
                // 'dlng'         => $outstation->drop_lng,
                'pick_address' => $request->pickup,
                // 'drop_address' => $request->drop
            ];
            $request_detail->requestHistory()->create($request_history_params);

            Customer::create([
                'request_id' => $request_detail->id,
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number,
                // 'customer_address' => $request->customer_address,
                'customer_slug' => $request->customer_slug,
                'status' => 1,
            ]);
        }
        else{
            $request_detail->package_id = $package->id;
            $request_detail->package_item_id = $packageItem->id;
            $request_detail->save();
        }

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$packageItem->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
           // dd($drivers);
            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        $selected_drivers[$noval] = $drivers_list;
                        $noval++;
                    }
                }
            }
            // dd($selected_drivers);
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }
        // dd($request->trip_type);
        if($request->trip_type == 'RIDE_NOW'){
            $selected_drivers = [];
            $result = fractal($request_detail, new TripRequestTransformer);
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$packageItem->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
            // dd($drivers);
            if ($drivers->success == true) {
                $is =0;
                foreach ($drivers->data as $key => $driver) {
                    $driverdet = User::where('slug',$driver->id)->first();
                    if($driverdet){
                        $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                        if($driverdet->active && $metta == 0){
                            $selected_drivers[$is]["user_id"] = $user->id;
                            $selected_drivers[$is]["driver_id"] = $driverdet->id;
                            $selected_drivers[$is]["active"] = ($key == 0 ? 1 : 0);
                            $selected_drivers[$is]["request_id"] = $request_detail->id;
                            $selected_drivers[$is]["assign_method"] = 1;
                            $selected_drivers[$is]["created_at"] = date('Y-m-d H:i:s');
                            $selected_drivers[$is]["updated_at"] = date('Y-m-d H:i:s');
                            $is++;
                        }
                    }
                }
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
            // dd($selected_drivers);
            $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
            
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
        }
        $request_detail = RequestModel::where('id',$request_detail->id)->first();
        // dd($request_detail);
        return $this->sendResponse('Request Trip Created Succassfully...', $request_detail, 200);
    }

    public function editRentalRide($request,$user)
    {
        if($request->pickup == ''){
            return $this->sendError('Required pickup or drop address',[],403);
        }
        $package = PackageMaster::where('slug',$request->package)->where('status',1)->first();
        if(is_null($package)){
            return $this->sendError('Invalid Package',[],403);
        }

        $packageItem = PackageItem::where('id',$request->type)->where('status',1)->first();
        if(is_null($packageItem)){
            return $this->sendError('Invalid Package type',[],403);
        }
        
        $request_detail = RequestModel::where('id',$request->ride_id)->first();
        if($request->trip_type == "RIDE_NOW"){
            $trip_type = "Ride Now";
            $later = false;
            $ride_date_time = NOW();
        }
        elseif($request->trip_type == "RIDE_LATER"){
            $trip_type = "Ride Later";
            $later = true;
            $ride_date_time = $request->ride_date_time ? $request->ride_date_time : date("Y-m-d H:i:s",strtotime($request_detail->trip_start_time));
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

            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($packageItem->type_id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }
            $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count > $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }

        $request_params = [
            'if_dispatch'             => true,
            'is_later'                => $later,
            'ride_type'				  => $trip_type,
            'trip_start_time'         => $ride_date_time,
            'request_otp'             => 1234, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'package_id'              => $package->id,
            'payment_opt'             => 'Cash',   
            'promo_id'                => $promocode_id,
            'package_item_id'         => $packageItem->id,
            'trip_type'               => $request->trip_types,
            'manual_trip'             => $request->manual_trip,
            'requested_currency_code' => $package->getCountry->currency_code,
            'driver_notes'            => $request->driver_notes,
            'requested_currency_symbol'=> $package->getCountry->currency_symbol,
        ];
        if($request->manual_trip == "AUTOMATIC"){
            $request_params['driver_id'] = NULL;
        }

        $request_detail = RequestModel::where('id',$request->ride_id)->update($request_params);
        $request_detail = RequestModel::where('id',$request->ride_id)->first();

        $request_place_params = [
            'pick_lat'     => $request->pickup_lat,
            'pick_lng'     => $request->pickup_lng,
            // 'drop_lat'     => $outstation->drop_lat,
            // 'drop_lng'     => $outstation->drop_lng,
            'pick_address' => $request->pickup,
            'pick_up_id'   => $request->pickup_lng_id,
            // 'drop_address' => $request->drop,
            // 'stops' => $request->stop ? 1 : 0

        ];

        $request_detail->requestPlace()->update($request_place_params);

        $request_history_params = [
            'olat'         => $request->pickup_lat,
            'olng'         => $request->pickup_lng,
            // 'dlat'         => $outstation->drop_lat,
            // 'dlng'         => $outstation->drop_lng,
            'pick_address' => $request->pickup,
            // 'drop_address' => $request->drop
        ];
        $request_detail->requestHistory()->update($request_history_params);

        Customer::where('request_id',$request->ride_id)->update([
            'request_id' => $request_detail->id,
            'customer_name' => $request->customer_name,
            'customer_number' => $request->customer_number,
            // 'customer_address' => $request->customer_address,
            'customer_slug' => $request->customer_slug,
            'status' => 1,
        ]);

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$packageItem->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
           // dd($drivers);
            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        $selected_drivers[$noval] = $drivers_list;
                        $noval++;
                    }
                }
            }
            // dd($selected_drivers);
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }
        // dd($request->trip_type);
        if($request->trip_type == 'RIDE_NOW'){
            $selected_drivers = [];
            $result = fractal($request_detail, new TripRequestTransformer);
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$packageItem->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
            // dd($drivers);
            if ($drivers->success == true) {
                $is =0;
                foreach ($drivers->data as $key => $driver) {
                    $driverdet = User::where('slug',$driver->id)->first();
                    if($driverdet){
                        $metta = RequestMeta::where('driver_id',$driverdet->id)->count();
                        if($driverdet->active && $metta == 0){
                            $selected_drivers[$is]["user_id"] = $user->id;
                            $selected_drivers[$is]["driver_id"] = $driverdet->id;
                            $selected_drivers[$is]["active"] = ($key == 0 ? 1 : 0);
                            $selected_drivers[$is]["request_id"] = $request_detail->id;
                            $selected_drivers[$is]["assign_method"] = 1;
                            $selected_drivers[$is]["created_at"] = date('Y-m-d H:i:s');
                            $selected_drivers[$is]["updated_at"] = date('Y-m-d H:i:s');
                            $is++;
                        }
                    }
                }
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
            // dd($selected_drivers);
            $metaDriver = User::where('id',$selected_drivers[0]['driver_id'])->first();
            
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
        }
        $request_detail = RequestModel::where('id',$request_detail->id)->first();
        // dd($request_detail);
        return $this->sendResponse('Request Trip Updated Succassfully...', $request_detail, 200);
    }

    public function outstationRide($request,$user,$create_id)
    {
        if($request->pickup == '' || $request->drop == ''){
            return $this->sendError('Required pickup or drop address',[],403);
        }
        
        if($request->type == ''){
            return $this->sendError('Required is type',[],403);
        }

        $outstation = OutstationMaster::where('drop',$request->drop)->first();
        if(is_null($outstation)){
            return $this->sendError('Invalid pickup or drop address',[],403);
        }

        $outstationPrice = OutstationPriceFixing::where('id',$request->type)->first();
        if(is_null($outstationPrice)){
            return $this->sendError('Invalid Vehicle type',[],403);
        }
        // dd($request);
        $promocode_id =0;
        if (request()->has('promo_code') && $request->promo_code != ""){
                
            $promocode = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
            if(is_null($promocode))
                return $this->sendError('Wrong Promo Code',[],403);
                    
            $promocode_id = $promocode->id;
            $promo_count = RequestModel::where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
            if($promo_count > $promocode->promo_user_reuse_count)
                return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);

            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($outstationPrice->type_id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }
            $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count > $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }

        $request_detail = RequestModel::leftJoin('request_places','request_places.request_id','=','requests.id')->where('requests.if_dispatch',1)->where('requests.user_id',$user->id)->where('pick_lat',$request->pickup_lat)->where('pick_lng',$request->pickup_lng)->where('drop_lat',$request->drop_lat)->where('drop_lng',$request->drop_lng)->where('requests.is_later',1)->where('requests.manual_trip',$request->manual_trip)->where('requests.is_trip_start',0)->where('requests.is_driver_started',0)->where('requests.is_cancelled',0)->whereNull('requests.driver_id')->select('requests.*')->first();
        if(!$request_detail){
            $requestNumber = generateRequestNumber();    
    
            $request_params = [
                'if_dispatch'             => true,
                'ride_type'               => "Ride Later",
                'is_later'                => true,
                'trip_start_time'         => $request->ride_date_time,
                'requested_currency_code' => $outstation->getCountry->currency_code,
                'requested_currency_symbol' => $outstation->getCountry->currency_symbol,
                'request_number'          => $requestNumber,
                'request_otp'             => 1234, // rand(1111, 9999),
                'user_id'                 => $user->id,
                'outstation_id'            => $outstation->id,
                'promo_id'                => $promocode_id,
                'payment_opt'             => 'Cash',   
                'outstation_type_id'      => $request->type,
                'trip_type'               => $request->trip_types,
                'driver_notes'            => $request->driver_notes,
                'manual_trip'             => $request->manual_trip,
                'outstation_trip_type'    => $request->way_trip,
                'trip_end_time'           => $request->ride_return_date_time,
                'created_by'              => $create_id,
            ];
            
            $request_detail = RequestModel::create($request_params);

            $request_place_params = [
                'pick_lat'     => $request->pickup_lat,
                'pick_lng'     => $request->pickup_lng,
                'pick_up_id'     => $request->pickup_lng_id,
                'drop_id'     => $request->drop_lng_id,
                'drop_lat'     => $request->drop_lat,
                'drop_lng'     => $request->drop_lng,
                'pick_address' => $request->pickup,
                'drop_address' => $request->drop,
                'poly_string'  => $request->poly_string,
                'drop_id'   => $request->drop_lng_id,
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
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number,
                // 'customer_address' => $request->customer_address,
                'customer_slug' => $request->customer_slug,
                'status' => 1,
            ]);
        }
        else{
            $request_detail->outstation_type_id = $request->type;
            $request_detail->save();
        }

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$outstationPrice->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        $selected_drivers[$noval] = $drivers_list;
                        $noval++;
                    }
                }
            }
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }

        return $this->sendResponse('Request Trip Created Succassfully...', $request_detail, 200);
    }

    

    public function editOutstationRide($request,$user)
    {
        if($request->pickup == '' || $request->drop == ''){
            return $this->sendError('Required pickup or drop address',[],403);
        }

        $outstation = OutstationMaster::where('drop',$request->drop)->first();
        if(is_null($outstation)){
            return $this->sendError('Invalid pickup or drop address',[],403);
        }

        $outstationPrice = OutstationPriceFixing::where('id',$request->type)->first();
        if(is_null($outstationPrice)){
            return $this->sendError('Invalid Vehicle type',[],403);
        }

        $request_detail = RequestModel::where('id',$request->ride_id)->first();
        // dd($request_detail);
        $promocode_id =0;
        if (request()->has('promo_code') && $request->promo_code != ""){
                
            $promocode = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
            if(is_null($promocode))
                return $this->sendError('Wrong Promo Code',[],403);
                    
            $promocode_id = $promocode->id;
            $promo_count = RequestModel::where('promo_id',$promocode_id)->where('user_id',$user->id)->where('is_completed',1)->count();
            if($promo_count > $promocode->promo_user_reuse_count)
                return $this->sendError('Sorry! You already '.$promocode->promo_user_reuse_count.' times used this promo code',[],403);

            if($promocode->select_offer_option == 1 && $promo_count >= $promocode->new_user_count){
                return $this->sendError('Sorry! You already '.$promocode->new_user_count.' times used this promo code',[],404);
            }
            if($promocode->select_offer_option == 5 && $user->id != $promocode->user_id){
                return $this->sendError('Invalid Prome code',[],404);
            }
            if(!in_array($outstationPrice->type_id,$promocode->types)){
                return $this->sendError('Invalid Prome code',[],404);
            }
            $promo_all_count = RequestModel::where('promo_id',$promocode_id)->where('is_completed',1)->count();
            if($promo_all_count > $promocode->promo_use_count)
                return $this->sendError('Sorry! promo code exit',[],403);
        }
        $request_params = [
            'if_dispatch'             => true,
            'trip_start_time'         => $request->ride_date_time ? $request->ride_date_time : date("Y-m-d H:i:s",strtotime($request_detail->trip_start_time)),
            'requested_currency_code' => $outstation->getCountry->currency_code,
            'requested_currency_symbol' => $outstation->getCountry->currency_symbol,
            'ride_type'               => "Ride Later",
            'is_later'                => true,
            'request_otp'             => 1234, // rand(1111, 9999),
            'user_id'                 => $user->id,
            'outstation_id'            => $outstation->id,
            'promo_id'                => $promocode_id,
            'payment_opt'             => 'Cash',   
            'outstation_type_id'      => $request->type,
            'trip_type'               => $request->trip_types,
            'driver_notes'            => $request->driver_notes,
            'manual_trip'             => $request->manual_trip
        ];
        if($request->manual_trip == "AUTOMATIC"){
            $request_params['driver_id'] = NULL;
        }
        
        $request_detail->update($request_params);

        $request_place_params = [
            'pick_lat'     => $request->pickup_lat,
            'pick_lng'     => $request->pickup_lng,
            'pick_up_id'     => $request->pickup_lng_id,
            'drop_id'     => $request->drop_lng_id,
            'drop_lat'     => $request->drop_lat,
            'drop_lng'     => $request->drop_lng,
            'pick_address' => $request->pickup,
            'drop_address' => $request->drop,
            'poly_string'  => $request->poly_string,
        ];
        if($request_detail->requestPlace){
            $request_detail->requestPlace()->update($request_place_params);
        }
        else{
            $request_detail->requestPlace()->create($request_place_params);
        }

        $request_history_params = [
            'olat'         => $request->pickup_lat,
            'olng'         => $request->pickup_lng,
            'dlat'         => $request->drop_lat,
            'dlng'         => $request->drop_lng,
            'pick_address' => $request->pickup,
            'drop_address' => $request->drop
        ];
        if($request_detail->requestHistory){
            $request_detail->requestHistory()->update($request_history_params);
        }
        else{
            $request_detail->requestHistory()->create($request_history_params);
        }

        Customer::where('request_id',$request_detail->id)->update([
            'request_id' => $request_detail->id,
            'customer_name' => $request->customer_name,
            'customer_number' => $request->customer_number,
            // 'customer_address' => $request->customer_address,
            'customer_slug' => $request->customer_slug,
            'status' => 1,
        ]);

        if($request_detail->manual_trip == 'MANUAL'){
            $selected_drivers = [];
            $drivers = fetchDrivers($request->pickup_lat,$request->pickup_lng,$outstationPrice->getVehicle->slug, $request->trip_types);
            $drivers = json_decode($drivers->getContent());
            if ($drivers->success == true) {
                $noval = 0;
                foreach ($drivers->data as $key => $value) {
                    $drivers_list = User::role('driver')->with('driver','driver.vehicletype')->where('slug',$value->id)->first();
                    if($drivers_list){
                        $drivers_list->trip_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->count();
                        $drivers_list->trip_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->count();
                        $drivers_list->trip_today_complete_count = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->where('is_cancelled',0)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $drivers_list->trip_today_cancel_count = RequestModel::where('driver_id',$value->id)->where('is_completed',0)->where('is_cancelled',1)->whereDate('accepted_at',date('Y-m-d'))->count();
                        $distance = $value->distance / 1000 / 50;
                        $time = (int)$distance * 60;
                        if($time == 0){
                            $time = 3;
                        }
                        $hours = $time / 60;
                        $minite = $time % 60;

                        $drivers_list->time = (int) $hours." hr ".(int) $minite." min";
                        $selected_drivers[$noval] = $drivers_list;
                        $noval++;
                    }
                }
            }
            $data = new \stdCLass();
            $data->result = $request_detail;
            $data->drivers = $selected_drivers;
            return $this->sendResponse('Data Found', $data, 200);
        }

        return $this->sendResponse('Request Trip Updated Succassfully...', $request_detail, 200);
    }
}
