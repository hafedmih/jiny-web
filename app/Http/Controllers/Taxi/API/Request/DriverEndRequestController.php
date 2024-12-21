<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\AdminCommissionType;
use App\Constants\PushEnum;
use App\Constants\RideType;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Taxi\API\Request\DriverEndRequest;
use App\Jobs\SendPushNotification;

use App\Models\taxi\Promocode;
use App\Models\taxi\Requests\RequestBill;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Settings;
use App\Models\taxi\Driver;
use App\Models\taxi\Requests\RequestHistory;
use App\Models\taxi\ZonePrice;
use App\Models\User;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\UserInstantTrip;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\Outofzone;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;

use Illuminate\Support\Facades\Http;
use App\Transformers\Request\TripRequestTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommanFunctions;

class DriverEndRequestController extends BaseController
{
    use CommanFunctions;
    public function endRequest(DriverEndRequest $request)
    {
    
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        // if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);
        $driver = $user->driver;
        $request_detail = $user->driverRequestDetail()->where('id', $request->request_id)->first();
        if (!$request_detail) {
            return $this->sendError('Unauthorized',[],401);
        }
     
        // Validate Trip request data
        if ($request_detail->is_completed) {
            // @TODO send success response with bill object
            $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');

            return $this->sendResponse('Data Found', $request_result, 200);
        }
     
        // dd($request_detail);

        if ($request_detail->is_cancelled) {
            return $this->sendError('Request Cancelled',[],401);
        }

        
        $request_place_params = ['drop_lat'=>$request->drop_lat,'drop_lng'=>$request->drop_lng,'drop_address'=>$request->drop_address];
        $request_place_history_params = ['dlat'=>$request->drop_lat,'dlng'=>$request->drop_lng,'drop_address'=>$request->drop_address];
       
        // Update Droped place details
        $request_detail->requestPlace->update($request_place_params);
        $request_detail->requestHistory->update($request_place_history_params);
        // Update Driver state as Available
        $request_detail->driverDetail->driver->update(['available'=>true]);

        $distance = 0;
        $givendistance = (double)$request->distance;

        $request_history = RequestHistory::where('olat','!=',Null)->where('olng','!=',Null)->where('dlat','!=',Null)->where('dlng','!=',Null)->where('request_id',$request_detail->id)->get();
        $calculatedDistance  = 0;

        if(!is_null($request_history)){
            foreach($request_history as $reques_data){
                $newdistance = $this->getDistance($reques_data->olat,$reques_data->olng,$reques_data->dlat,$reques_data->dlng);
                $calculatedDistance = $calculatedDistance+$newdistance;
            }
        }
        if($givendistance > $calculatedDistance){
            $distance = $givendistance;
        }else{
            $distance = $calculatedDistance;
        }
       // $distance = $givendistance;

        if($request_detail->trip_type == 'OUTSTATION'){
            if($request->has('trip_image') && $request->trip_image != "" && $request->has('end_km') && $request->end_km != ""){
                $filename =  uploadImage('images/outstation',$request->file('trip_image'));
                $outstation = OutstationUploadImages::where('request_id',$request_detail->id)->first();
                // dd($outstation);
                $distance = $request->end_km - $outstation->trip_start_km;
                $outstation->trip_end_km_image = $filename;
                $outstation->trip_end_km = $request->end_km;
                $outstation->distance = $distance;
                $outstation->save();

                $requests = RequestModel::where('id',$request_detail->id)->first();

                $user_slug_get = User::where('id',$requests->user_id)->first();

                $out_station_upload = OutstationUploadImages::where('request_id',$request_detail->id)->first();

                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = "";
                $socket_data->result = ['trip_end_km' => $out_station_upload->trip_end_km_image];
                $socketData = ['event' => 'kilometer_upload_'.$user_slug_get->slug,'message' => $socket_data];
                sendSocketData($socketData);


            }
            else{
                return $this->sendError('Trip start km and image is required',[],401);
            }
        }
        
        if($request->has('trip_image') && $request->trip_image != "" && $request->has('end_km') && $request->end_km != ""){
            if($request_detail->trip_type == 'RENTAL'){
                if($request->has('trip_image') && $request->trip_image != "" && $request->has('end_km') && $request->end_km != ""){
                    $filename =  uploadImage('images/outstation',$request->file('trip_image'));
                    $outstation = OutstationUploadImages::where('request_id',$request_detail->id)->first();
                    // dd($outstation);
                    $distance = $request->end_km - $outstation->trip_start_km;
                    $outstation->trip_end_km_image = $filename;
                    $outstation->trip_end_km = $request->end_km;
                    $outstation->distance = $distance;
                    $outstation->save();

                    $requests = RequestModel::where('id',$request_detail->id)->first();

                    $user_slug_get = User::where('id',$requests->user_id)->first();
    
                    $out_station_upload = OutstationUploadImages::where('request_id',$request_detail->id)->first();
    
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = "";
                    $socket_data->result = ['trip_end_km' => $out_station_upload->trip_end_km_image];

                    $socketData = ['event' => 'kilometer_upload_'.$user_slug_get->slug,'message' => $socket_data];
                    sendSocketData($socketData);


                }
                else{
                    return $this->sendError('Trip end  km and image is required',[],401);
                }
            }
        }

        
         
        $duration = $this->calculateDurationOfTrip($request_detail->trip_start_time);
        
        if($request_detail->payment_opt=="Cash"){

            $paid_status = 1;

             $user_wallet = Wallet::where('user_id',$request_detail->user_id)->first();

            if($user_wallet){
                $driver_wallet = Wallet::where('user_id',$request_detail->driver_id)->first();
               
               
               if($request_detail->wallet_deduct_amount != null){
                    if($user_wallet->balance_amount >= $request_detail->wallet_deduct_amount){
                        /** User Wallet */
                        $user_wallet->amount_spent += $request_detail->wallet_deduct_amount;
                        $user_wallet->balance_amount -= $request_detail->wallet_deduct_amount;
                        $user_wallet->save();

                        $wallet_transaction = WalletTransaction::create([
                            'wallet_id' => $user_wallet->id,
                            'amount' =>  $request_detail->wallet_deduct_amount,
                            'purpose' => 'Trip amount transfer successfully',
                            'type' => 'SPENT',
                            'user_id' => $request_detail->user_id,
                            'request_id' => $request_detail->id
                        ]);
                        $rider = User::where('id',$request_detail->user_id)->first();
                        if ($rider) {
                            $title = Null;
                            $body = '';
                            $lang = $rider->language;
                            $push_data = $this->pushlanguage($lang,'driver-trip-amount');
                            if(is_null($push_data)){
                                $title     = $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount . " has been debited from your account.";
                                $body      = $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount . " has been debited from your account.";
                                $sub_title = $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount . " has been debited from your account.";

                            }else{
                                $title     =  $push_data->title;
                                $body      =  $push_data->description;
                                $sub_title =  $push_data->description;
            
                            }   
                            
                        // @ TODO Driver get push and socket
            
                            // Form a socket structure using users'id and message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message  = PushEnum::TRIP_AMOUNT_DEBIT;
                            $socket_data->trip_wallet_amount  = $request_detail->wallet_deduct_amount;
                            $socket_data->currency_symbol  = $request_detail->requested_currency_symbol;

                            $socketData = ['event' => 'user_trip_wallet_amount_'.$rider->slug,'message' => $socket_data];
                            sendSocketData($socketData);
                        
                            $pushData = ['notification_enum' => PushEnum::TRIP_AMOUNT_DEBIT];
                            // dispatch(new SendPushNotification($title,$pushData, $rider->device_info_hash, $rider->mobile_application_type,0,$sub_title));
                            sendPush($title,$sub_title, $pushData, $rider->device_info_hash, $rider->mobile_application_type,0);
                        }

                        /** Driver wallet */
                        if(is_null($driver_wallet)){
                            $driver_wallet = new Wallet();
                            $driver_wallet->user_id = $request_detail->driver_id;
                            $driver_wallet->earned_amount = $request_detail->wallet_deduct_amount;
                            $driver_wallet->balance_amount = $request_detail->wallet_deduct_amount;
                            $driver_wallet->save();
                        }else{
                            $driver_wallet->earned_amount	+= $request_detail->wallet_deduct_amount;
                            $driver_wallet->balance_amount += $request_detail->wallet_deduct_amount;
                            $driver_wallet->save();
                        }
                        

                        $wallet_transaction = WalletTransaction::create([
                            'wallet_id' => $user_wallet->id,
                            'amount' =>  $request_detail->wallet_deduct_amount,
                            'purpose' => 'Trip amount credited successfully',
                            'type' => 'EARNED',
                            'user_id' => $request_detail->driver_id,
                            'request_id' => $request_detail->id
                        ]);
                        $trip_driver = User::where('id',$request_detail->driver_id)->first();
                    
                        if ($trip_driver) {
                            $title = Null;
                            $body = '';
                            $lang = $trip_driver->language;
                            $push_data = $this->pushlanguage($lang,'driver-trip-amount');
                            if(is_null($push_data)){
                                $title     = "Your account has been credited with ". $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount ;
                                $body      = "Your account has been credited with ". $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount ;
                                $sub_title = "Your account has been credited with ". $request_detail->requested_currency_symbol . ' ' . $request_detail->wallet_deduct_amount ;
                            }else{
                                $title     =  $push_data->title;
                                $body      =  $push_data->description;
                                $sub_title =  $push_data->description;
            
                            }   
                        
                        // @ TODO Driver get push and socket
            
                            // Form a socket structure using users'id and message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message  = PushEnum::TRIP_AMOUNT_CREDIT;
                            $socket_data->trip_wallet_amount  = $request_detail->wallet_deduct_amount;
                            $socket_data->currency_symbol  = $request_detail->requested_currency_symbol;

                            $socketData = ['event' => 'driver_trip_wallet_amount_'.$trip_driver->slug,'message' => $socket_data];
                            sendSocketData($socketData);
            
                            $pushData = ['notification_enum' => PushEnum::TRIP_AMOUNT_CREDIT];
                        
                            dispatch(new SendPushNotification($title,$pushData, $trip_driver->device_info_hash, $trip_driver->mobile_application_type,0,$sub_title));
                            
                        }
                    
                        $request_detail->save();
                    }else{
                        return $this->sendError('insufficient balance.',[],403);
                    }
                }
            }

        }else {

            $paid_status = 0;

        }

        $waitingTime = $request->waiting_time;
        $requestParams = [
            'is_completed'   => 1,
            'completed_at'   =>  Carbon::now(),
            'total_distance' => $distance,
            'total_time'     => $duration,
            'is_paid'        => $paid_status,
            'trip_end_time'  => $request->trip_end_time
        ];

   
         $request_detail->update($requestParams);  

       User::where('id',$request_detail->user_id)->increment('trips_count');
       User::where('id',$request_detail->driver_id)->increment('trips_count');

       $this->referalAmountTreansfer($request_detail->user_id);
       $this->referalAmountTreansfer($request_detail->driver_id);

        $promo_detail =null;
        if ($request_detail->promo_id) {
            $promo_detail = $this->validateAndGetPromoDetail($request_detail);
        }
        $promocode = Promocode::where('id',$request_detail->promo_id)->first();

        if($promocode){
            $promo_total_count = RequestModel::where('promo_id',$promocode->id)->where('is_completed',1)->count();

            if($promo_total_count >= $promocode->promo_use_count){
                $promocode->status = 0;
                $promocode->save();
            }
        }
       
       
           
        
        $calculated_bill =  $this->calculateRideFares($distance, $duration, $waitingTime, $request_detail, $promo_detail,$request->drop_lat,$request->drop_lng,$request->close_trip);

        //    dd($calculated_bill);
        // @TODO need to take admin commission from driver wallet
        if ($request_detail->payment_opt == 'Cash') {
            //Detect the Admin commission 
            $this->walletTransaction($calculated_bill['admin_commision'],$request_detail->driver_id,'SPENT','Admin Commission',$request_detail->id);

            //Detect the service tax
            $this->walletTransaction($calculated_bill['service_tax'],$request_detail->driver_id,'SPENT','Service Tax',$request_detail->id);
            
        }

        // dd($calculated_bill);
        $calculated_bill['request_id'] = $request_detail->id;
        $calculated_bill['requested_currency_code'] = $request_detail->requested_currency_code;
        $calculated_bill['requested_currency_symbol'] = $request_detail->requested_currency_symbol;
        if($request_detail->destination_type == "NORMAL"){
            $calculated_bill['base_price'] = $request_detail->base_price;
            $calculated_bill['distance_price'] = $request_detail->distance_cost;
            $calculated_bill['total_amount'] = $request_detail->amount;
            $calculated_bill['driver_commision'] = $request_detail->amount - $calculated_bill['admin_commision'];
            if(!is_null($request_detail->wallet_deduct_amount)){
                $calculated_bill['cash_on_hand'] = $request_detail->amount - $request_detail->wallet_deduct_amount;
                $calculated_bill['wallet_credit_amount'] = $request_detail->wallet_deduct_amount;
            }else{
                $calculated_bill['cash_on_hand'] = $request_detail->amount;
            }
        }else{
            // $calculated_bill['base_price'] = $request_detail->base_price;
            // $calculated_bill['total_amount'] = $request_detail->amount;
            // $calculated_bill['driver_commision'] = $calculated_bill['total_amount'] - $calculated_bill['admin_commision'];
            if(!is_null($request_detail->wallet_deduct_amount)){
                $calculated_bill['cash_on_hand'] = $calculated_bill['total_amount'] - $request_detail->wallet_deduct_amount;
                $calculated_bill['wallet_credit_amount'] = $request_detail->wallet_deduct_amount;
            }else{
                $calculated_bill['cash_on_hand'] = $calculated_bill['total_amount'];
            }
        }
       
        $request_bill = RequestBill::where('request_id',$request_detail->id)->delete();
        $request_bill = RequestBill::create($calculated_bill);
        // dd($request_bill);
        
        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
        if($request_detail->is_instant_trip != 1){
            if ($request_detail->user_id != null) {
                $push_request_detail = $request_result->toJson();
                $userModel = User::find($request_detail->user_id);

                $title = Null;
                $body = '';
                $lang = $userModel->language;
                $push_data = $this->pushlanguage($lang,'trip-end');
                if(is_null($push_data)){
                    $title = 'Thanks for choosing Lahagni';
                    $body = 'Driver finished the ride, Please help us by rate the driver';
                    $sub_title = 'Driver finished the ride, Please help us by rate the driver';

                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                } 

                // if($userModel->email){
                //     $settings = Settings::where('status',1)->pluck('value','name')->toArray();
                //     $pdf = \PDF::loadView('emails.RequestBillMailPDF',['settings' => $settings,'request_detail' => $request_detail]);
                //     \Mail::to($userModel->email)->send(new \App\Mail\MyTestMail($request_detail,$settings,$pdf));
                // }

                $push_data = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP];

                // Form a socket sturcture using users'id and message with event name
                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = PushEnum::DRIVER_END_THE_TRIP;
                $socket_data->result = $request_result;

                $socketData = ['event' => 'request_'.$userModel->slug,'message' => $socket_data];
                sendSocketData($socketData);
       
                // $pushData = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP, 'result' => (string) $request_result->toJson()];
                $pushData = ['notification_enum' => PushEnum::DRIVER_END_THE_TRIP];
                // dispatch(new SendPushNotification($title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0,$sub_title));

                sendPush($title,$sub_title, $pushData, $userModel->device_info_hash, $userModel->mobile_application_type,0);
            }
        }else{
            $userModel = User::find($request_detail->user_id);
            if(!$userModel){
                $userModel = UserInstantTrip::find($request_detail->user_id);
            }
            //send SMS to User

            //Hi , Thanks for using our taxi service ,You bill Amount is {#var#}, Download user app {#var#} - NPTECH


           
            // $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$userModel->phone_number.'&message=Hi , Thanks for using our taxi service ,You bill Amount is '.$calculated_bill['total_amount'].', Download user app https://bit.ly/3wqvFRP - NPTECH');
            // $post = json_decode($data->getBody()->getContents());
                

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
                    'purpose' => "Referal Amount",
                    'type' => "EARNED",
                    'user_id' => $refer_user->id
                ]);
                Driver::where('user_id',$refer_user->id)->update(['refernce_count' => 0]);
            }
        }
        
        return $this->sendResponse('Data Found', $request_result, 200);
    }

    /**
     * Calculate trip time 
     * 
    */
    public function calculateDurationOfTrip($start_time)
    {
        $start_time = Carbon::parse($start_time);
        
        $total_duration = now()->diffInMinutes($start_time);

        return $total_duration;
    }

    /**
     * Get promo detail 
     * 
    */
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

    /**
     * Calculare ride fares
     * 
    */
    public function calculateRideFares($distance, $duration, $waitingTime, $request_detail, $promo_detail,$drop_lat,$drop_lng,$close_trip)
    {
        // dd($request_detail->trip_type);
        if($request_detail->trip_type == 'OUTSTATION'){
            $outstation_price = OutstationPriceFixing::where('id',$request_detail->outstation_type_id)->first();

            $startDistance = OutstationUploadImages::where('request_id',$request_detail->id)->first();
            $finalDistance = (int)$startDistance->trip_end_km - (int)$startDistance->trip_start_km ;
            if($finalDistance < 0 )
                $finalDistance = 0;
            
            
            
            $outstationmaster = OutstationMaster::where('id',$request_detail->outstation_id)->first();
           
                $distance_price = $outstation_price->distance_price;
            if($request_detail->outstation_trip_type == 'TWO'){

                $distance_price = $outstation_price->distance_price_two_way;
            }

            
           
            if($request_detail->getAssignAmount && $request_detail->getAssignAmount->amount_per_km){
                $distance_price = $request_detail->getAssignAmount->amount_per_km;
            }
            $admin_commission = $outstation_price->admin_commission;
            $admin_commission_type = $outstation_price->admin_commission_type;
            $waiting_charge = $outstation_price->waiting_charge;
            $grace_time = $outstation_price->grace_time;
            $driver_price = 0;

            if($request_detail->outstation_trip_type == 'ONE'){

                $finalDistance = $finalDistance  *2;
                if($finalDistance < $outstation_price->minimum_km)
                    $driver_price = $outstation_price->base_fare;
                else
                    $driver_price = $outstation_price->driver_price;

            }else{
                $driverSinglePrice = $outstation_price->day_rent_two_way;
                $duration12 = number_format($duration/60,2);
                if($duration12 > 0){
                    if($duration12 <= 12){
                        $driver_price = $driverSinglePrice;
                        
                    }else{
                        $newdrivertime = $duration12 - 12;
                        if($newdrivertime <= 24){
                            $driver_price = $driverSinglePrice * 2;
                        }else{
                            $aa = $newdrivertime / 24 ;
                            $remainder = $newdrivertime % 24 ;
                            $driver_price = ($driverSinglePrice * floor($aa)) + $driverSinglePrice ;

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
               // $distance_price = ($distance ) * $value->distance_price_two_way;
            }



            // $total_distance_amount = ($distance_price * $finalDistance) * 2;
            $total_distance_amount = ($distance_price * $finalDistance) ;

            // if($waitingTime <0)
            //     $waitingTime = 0;

            $total__trip_time = $duration ;
            // $total__trip_time = $duration - $waitingTime;

            // $waitingprice = $waitingTime * $waiting_charge;
            
            // $subTotal = $total_distance_amount  + $waitingprice;
            $subTotal = $total_distance_amount ;

            if($admin_commission_type == '1'){
                $admin_commission_amount = $subTotal * $admin_commission/100;
            }
            else{
                if($subTotal < $admin_commission)
                    $admin_commission_amount  = 0;
                else
                    $admin_commission_amount = $admin_commission;
            }
            // dd($admin_commission_amount);
           
            
            //Calculate Service Tax
            $servicetax = 0;
            $servicetaxfetch = Settings::where('name','service_tax')->first();
            if(is_null($servicetaxfetch)){
                $servicetax = 5;
            }
            if($servicetaxfetch->value == null){
                $servicetax = 5;
            }else{
                $servicetax = $servicetaxfetch->value;
            }

            $discount_amount = 0;
            // Promo Discount
            $amount_without_promo = $subTotal;
            
            if (!is_null($promo_detail)) {
                
                if($promo_detail->promo_type == 1){
                    $discount_amount = $promo_detail->amount;
                }else if($promo_detail->promo_type == 2){
                    $discount_amount = ($promo_detail->percentage / 100) * $subTotal;
                }
                if($promo_detail->select_offer_option == 4 && $promo_detail->from_date <= date('Y-m-d') && $promo_detail->to_date >= date('Y-m-d') || $promo_detail->select_offer_option != 4){
                    if($subTotal >= $promo_detail->target_amount){
                        if ($discount_amount < $subTotal) {
                            //    echo $subTotal."---";
                            $subTotal = $subTotal - $discount_amount;
                            // dd($subTotal);
                        }else{
                            $subTotal = 0;
                        }
                    }
                    else{
                        $discount_amount = 0;
                        $request_detail->promo_id = 0;
                        $request_detail->save();
                    }
                }
                else{
                    $discount_amount = 0;
                    $request_detail->promo_id = 0;
                    $request_detail->save();
                }
            }


            $servicetaxamount = ($subTotal * (float)$servicetax) / 100;

            $driver_commission = ($subTotal - $servicetaxamount - $admin_commission_amount) ;

            $hillstation_price = 0;
            if($outstationmaster->hill_station == 'YES'){
                $hillstation_price =  $outstation_price->hill_station_price;
            }

            $driver_commission = $driver_commission + $driver_price + $hillstation_price;


            $total = $subTotal + $driver_price + $hillstation_price;

            if($request_detail->getAssignAmount && $request_detail->getAssignAmount->request_amount){
                $total = $request_detail->getAssignAmount->request_amount;
                $subTotal = $request_detail->getAssignAmount->request_amount;
                $driver_price = 0;
                $distance_price = 0;
                $total_distance_amount = 0;
                $discount_amount = 0;
                if($admin_commission_type == '1'){
                    $admin_commission_amount = $subTotal * $admin_commission/100;
                }
                else{
                    if($subTotal < $admin_commission)
                        $admin_commission_amount  = 0;
                    else
                        $admin_commission_amount = $admin_commission;
                }
                $servicetaxamount = ($subTotal * (float)$servicetax) / 100;
                $driver_commission = ($subTotal - $servicetaxamount - $admin_commission_amount) ;
                $hillstation_price = 0;
            }

            return [
                'base_price'                => $driver_price,
                'base_distance'             => 0,
                'price_per_distance'        => $distance_price,
                'distance_price'            => $total_distance_amount,
                'price_per_time'            => 0,
                'time_price'                => 0,
                'promo_discount'            => $discount_amount,
                'waiting_charge'            => 0,
                'admin_commision'           => $admin_commission_amount,
                'driver_commision'          => $driver_commission,
                'total_amount'              => $total,
                'sub_total'                 => $subTotal,
                'total_distance'            => $finalDistance,
                'total_time'                => $duration,
                'requested_currency_code'   => $request_detail->requested_currency_code,
                'requested_currency_symbol' => $request_detail->requested_currency_symbol,
                'out_of_zone_price'         => 0,
                'booking_fees'              => 0,
                'service_tax'               => $servicetaxamount,
                'service_tax_percentage'    => $servicetax,
                'hill_station_price'       => $hillstation_price,

            ];

          

        }
        elseif($request_detail->trip_type == 'RENTAL'){
            // fetch the data from tables 
            
            $packageBaseFare = PackageMaster::where('is_base_package','YES')->first();
            $package = PackageMaster::where('id',$request_detail->package_id)->first();
            $package_item = PackageItem::where('id',$request_detail->package_item_id)->first();

            $packageBaseItem = PackageItem::where('package_id',$packageBaseFare->id)->where('type_id',$package_item->type_id)->first();

            $package_price = $packageBaseItem->price;
            $package_hour = $packageBaseFare->hours;
            $package_km = $packageBaseFare->km;
            $admin_commission = $package->admin_commission;
            $admin_commission_type = $package->admin_commission_type;
            $driver_price = $package->driver_price;

           // calculate the Price per km and per min

            // 1. Calculate the price split by km and hour 
            $half_package_price = $package_price / 2;
             
            // 2. Convert hour into min
            $hour_to_min = $package_hour * 60;
           
            //3. Calculate the hour price
            $per_mintute_price =  $half_package_price/$hour_to_min ; 
          
            //4. Calculate the per km price
            $per_km_price =   $half_package_price/$package_km;

           
            $subTotal = 0;
            $distance_price = 0;
            $time_price = 0;
            $baseprice =0;
           
           // convert min into hours
            $hours = floor($duration / 60);
            $min = $duration - ($hours * 60);
            $triphours = $hours;
            if($hours == 0){
                $triphours = 1;
            }
            
            if($min > 5  & $min < 30 ){
                    $min = 30;
            }elseif($min > 30){
                $triphours += 1;
                $min = 0;
            }else{
                $min = 0;
            }

            $packagecost = (($triphours *60 ) * $per_mintute_price) ;
            $time_price = ($min * $per_mintute_price);

            $packagedistance = $triphours * 10;

            $packagecost += $packagedistance * $per_km_price;
            $pendingKm = 0;
            if($distance > $packagedistance){
                $pendingKm = $distance - $packagedistance;
                $distance_price = $pendingKm * $per_km_price;
            }
            $subTotal =  $distance_price + $packagecost +$time_price;

           
            if($admin_commission_type == '1'){
                $admin_commission_amount = $subTotal * $admin_commission/100;
            }
            else{
                if($subTotal < $admin_commission)
                    $admin_commission_amount  = 0;
                else
                    $admin_commission_amount = $admin_commission;
            }
            // dd($admin_commission_amount);
            

            
            //Calculate Service Tax
            $servicetax = 0;
            $servicetaxfetch = Settings::where('name','service_tax')->first();
            if(is_null($servicetaxfetch)){
                $servicetax = 5;
            }
            if($servicetaxfetch->value == null){
                $servicetax = 5;
            }else{
                $servicetax = $servicetaxfetch->value;
            }

            $discount_amount = 0;
            // Promo Discount
            $amount_without_promo = $subTotal;
            
            if (!is_null($promo_detail)) {
                
                if($promo_detail->promo_type == 1){
                    $discount_amount = $promo_detail->amount;
                }else if($promo_detail->promo_type == 2){
                    $discount_amount = ($promo_detail->percentage / 100) * $subTotal;
                }
                if($promo_detail->select_offer_option == 4 && $promo_detail->from_date <= date('Y-m-d') && $promo_detail->to_date >= date('Y-m-d') || $promo_detail->select_offer_option != 4){
                    if($subTotal >= $promo_detail->target_amount){
                        if ($discount_amount < $subTotal) {
                            //    echo $subTotal."---";
                            $subTotal = $subTotal - $discount_amount;
                            // dd($subTotal);
                        }else{
                            $subTotal = 0;
                        }
                    }
                    else{
                        $discount_amount = 0;
                        $request_detail->promo_id = 0;
                        $request_detail->save();
                    }
                }
                else{
                    $discount_amount = 0;
                    $request_detail->promo_id = 0;
                    $request_detail->save();
                }
            }

            // $total = $subTotal;
            $servicetaxamount = ($subTotal * (float)$servicetax) / 100;

            $driver_commission = ($subTotal - $servicetaxamount - $admin_commission_amount) ;
            $total = $subTotal;

            if($request_detail->getAssignAmount){
                $total = $request_detail->getAssignAmount->request_amount;
                $amount_without_promo = $request_detail->getAssignAmount->request_amount;
                $baseprice = 0;
                $per_km_price = 0;
                $distance_price = 0;
                $per_mintute_price = 0;
                $time_price = 0;
                $discount_amount = 0;
                if($admin_commission_type == 1){
                    $admin_commission_amount = $total * $admin_commission/100;
                }
                else{
                    if($total < $admin_commission)
                        $admin_commission_amount  = 0;
                    else
                        $admin_commission_amount = $admin_commission;
                }
                $servicetaxamount = ($total * (float)$servicetax) / 100;
                $driver_commission = ($total - $servicetaxamount - $admin_commission_amount) ;
            }

            return [
                'base_price'                => $packagecost,
                'base_distance'             => 0,
                'price_per_distance'        => $per_km_price,
                'distance_price'            => $distance_price,
                'price_per_time'            => $per_mintute_price,
                'time_price'                => $time_price,
                'promo_discount'            => $discount_amount,
                'waiting_charge'            => 0,
                'admin_commision'           => $admin_commission_amount,
                'driver_commision'          => $driver_commission,
                'total_amount'              => $total,
                'sub_total'                 => $amount_without_promo,
                'total_distance'            => $distance,
                'total_time'                => $duration,
                'requested_currency_code'   => $request_detail->requested_currency_code,
                'requested_currency_symbol' => $request_detail->requested_currency_symbol,
                'out_of_zone_price'         => 0,
                'booking_fees'              => 0,
                'service_tax'               => $servicetaxamount,
                'service_tax_percentage'    => $servicetax,
                'package_hours'    => $triphours,
                'package_km'    => $packagedistance,
                'pending_km'    => $pendingKm,

            ];

        }
        elseif($request_detail->trip_type == 'LOCAL'){ 
           
            $price = ZonePrice::whereId($request_detail->zone_type_id)->first();
        
            if ($request_detail->ride_type == RideType::RIDENOW) {
                $basePrice     = $price->ridenow_base_price;
                $distancePrice = $price->ridenow_price_per_distance;
                $timePrice     = $price->ridenow_price_per_time;
                $baseDistance  = (double)$price->ridenow_base_distance;
                $freeWaiting   = $price->ridenow_free_waiting_time;
                $waitingCharge = $price->ridenow_waiting_charge;
                $bookingBase_fee = $price->ridenow_booking_base_fare;
                $bookingDistancefee = $price->ridenow_booking_base_per_kilometer;
            } else {
                $basePrice     = $price->ridelater_base_price;
                $distancePrice = $price->ridelater_price_per_distance;
                $timePrice     = $price->ridelater_price_per_time;
                $baseDistance  = (double)$price->ridelater_base_distance;
                $freeWaiting   = $price->ridelater_free_waiting_time;
                $waitingCharge = $price->ridelater_waiting_charge;
                $bookingBase_fee = $price->ridelater_booking_base_fare;
                $bookingDistancefee = $price->ridelater_booking_base_per_kilometer;
            }

            if($request_detail->destination_type == "OPEN"){
                $basePrice     = $price->open_base_price;
                $distancePrice = $price->open_price_per_distance;
                $timePrice     = $price->open_price_per_time;
                $baseDistance  = (double)$price->open_base_distance;
                $freeWaiting   = $price->open_free_waiting_time;
                $waitingCharge = $price->open_waiting_charge;
                $bookingBase_fee = $price->open_booking_base_fare;
                $bookingDistancefee = $price->open_booking_base_per_kilometer;
            }

            $givendistance = $distance;
            // Surge price
            $today = now()->dayName;
            foreach ($price->getSurgePrice as $key => $surge) {
                $startDate = now()->parse($surge->start_time);
                $endDate = now()->parse($surge->end_time);

                if(now()->gte($startDate) && now()->lte($endDate) && $surge->available_days){
                    $exploded_days=explode(',',$surge->available_days);

                    if(in_array($today,$exploded_days)){
                        $basePrice = $surge->surge_price;
                        $distancePrice = $surge->surge_distance_price;
                        break;
                    }
                }
            }
            
            
            $booking_km_amount = 0;
            if($price->ridenow_booking_base_fare != 0){
                if($distance > $baseDistance){
                    $balance_distance = $distance - $baseDistance;
                    $booking_km_amount = $balance_distance * $price->ridelater_booking_base_per_kilometer;
                }
            }
            
            if($request_detail->destination_type == "OPEN"){
                if($price->open_booking_base_fare != 0){
                    if($distance > $baseDistance){
                        $balance_distance = $distance - $baseDistance;
                        $booking_km_amount = $balance_distance * $price->open_booking_base_per_kilometer;
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
            $bookingbaseprice = 0;
            $booking_km_amount = 0;
            $price->ridenow_booking_base_fare = 0;

           
            $subTotal = $subTotal + $booking_km_amount + $price->ridenow_booking_base_fare;

            // if($bookingBase_fee > 0){
            //     $bookingbaseprice = $distancePrice * $bookingBase_fee;
            //     $subTotal = $subTotal+$bookingbaseprice;
            // }
            // $bookingDistancePrices = 0;
            // if($bookingDistancefee > 0){
            //     $bookingDistancePrices = $bookingDistancefee * $distance;
            //     $subTotal = $subTotal+$bookingDistancePrices;
            // }
            $total_booking_price = $price->ridenow_booking_base_fare + $booking_km_amount;
            
            $driver_details = Driver::where('user_id',$request_detail->driver_id)->first();
            $subscription = DriverSubscriptions::where('user_id',$request_detail->driver_id)->where('from_date','<=',NOW())->where('to_date','>=',NOW())->first();

            $adminServiceFee = 0;
            if($driver_details && $driver_details->subscription_type == 'SUBSCRIPTION'){
                $adminServiceFee = 0;
            }

            if($driver_details && $driver_details->subscription_type == 'BOTH' && $subscription){
                $adminServiceFee = 0;
            }
        
            $outofzone = 0;
            
            
            $out_of_zone_price = 0;
            $drop_zone = $this->getZone($drop_lat,$drop_lng);
            
            if(!$drop_zone){
                $int_distance = (int)$distance;

                $outofzone = Outofzone::orderby('id','desc')->get();

                foreach($outofzone as $out){
                    if($out->kilometer >= $int_distance){
                        $out_of_zone_price = $out->price;
                    }
                    else{
                        $outofzone1 = Outofzone::orderby('id','desc')->first();
                        $out_of_zone_price = $outofzone1->price;
                    }
                }

                
            }else{
               
                if($drop_zone->non_service_zone == 'Yes'){
                    $int_distance = (int)$distance;
                    $outofzone = Outofzone::orderby('id','desc')->get();
                    foreach($outofzone as $out){
                        if($out->kilometer >= $int_distance){
                            $out_of_zone_price = $out->price;
                        }
                        else{
                            $outofzone1 = Outofzone::orderby('id','desc')->first();
                            $out_of_zone_price = $outofzone1->price;
                        }
                    }
                }
                
            }
    
            //Calculate Service Tax
            $servicetax = 0;
            $servicetaxfetch = Settings::where('name','service_tax')->first();
            if(is_null($servicetaxfetch)){
                $servicetax = 5;
            }
            if($servicetaxfetch->value == null){
                $servicetax = 5;
            }else{
                $servicetax = $servicetaxfetch->value;
            }

            $discount_amount = 0;
            $subTotal =  $subTotal + $out_of_zone_price;
            $total = $subTotal;
            if (!is_null($promo_detail)) {
                
                if($promo_detail->promo_type == 1){
                    $discount_amount = $promo_detail->amount;
                }else if($promo_detail->promo_type == 2){
                    $discount_amount = ($promo_detail->percentage / 100) * $subTotal;
                }
                if($promo_detail->select_offer_option == 4 && $promo_detail->from_date <= date('Y-m-d') && $promo_detail->to_date >= date('Y-m-d') || $promo_detail->select_offer_option != 4){
                    // dump($discount_amount);
                    // dump($promo_detail->target_amount);
                    if($subTotal >= $promo_detail->target_amount){
                        // dump($discount_amount);
                        // dump($subTotal);
                        if ($discount_amount < $subTotal) {
                            //    echo $subTotal."---";
                            $servicetaxamount = ($subTotal * (float)$servicetax) / 100;

                            $admin_amount = $servicetaxamount+$adminServiceFee;
                            if($admin_amount < $discount_amount){
                                $driver_bounes = $discount_amount - $admin_amount;

                                $this->walletTransaction($driver_bounes,$request_detail->driver_id,'EARNED','Trip Bonus',$request_detail->id);
                            }
                            $subTotal = $subTotal - $discount_amount;
                            // dd($subTotal);
                        }else{
                            $subTotal = 0;
                        }
                    }
                    else{
                        $discount_amount = 0;
                        $request_detail->promo_id = 0;
                        $request_detail->save();
                    }
                }
                else{
                    $discount_amount = 0;
                    $request_detail->promo_id = 0;
                    $request_detail->save();
                }
            }

            $adminCommission = $request_detail->is_later == "1" ? $price->ridelater_admin_commission : $price->ridenow_admin_commission;

            $adminCommissionType = $request_detail->is_later == "1" ? $price->ridelater_admin_commission_type : $price->ridenow_admin_commission_type;
            
            if($request_detail->destination_type == "OPEN"){
                $adminCommission = $price->open_admin_commission;

                $adminCommissionType = $price->open_admin_commission_type;
            }
            
            if ($adminCommissionType == AdminCommissionType::PERCENTAGE) {
                if($request_detail->destination_type == 'NORMAL'){
                    $adminServiceFee = ($request_detail->amount * ($adminCommission / 100));
                }
                else{
                    $adminServiceFee = ($subTotal * ($adminCommission / 100));
                }
            
            } else {
                $adminServiceFee = $adminCommission;
            }
            
            $servicetaxamount = ($total * (float)$servicetax) / 100;
            $driverCommission = ($subTotal - $adminServiceFee -$servicetaxamount) > 0 ? $subTotal - $adminServiceFee - $servicetaxamount: 0;
            // Promo Discount
            $amount_without_promo = $subTotal;

            if($request_detail->getAssignAmount){
                $subTotal = $request_detail->getAssignAmount->request_amount;
                $total = $request_detail->getAssignAmount->request_amount;
                $servicetaxamount = ($total * (float)$servicetax) / 100;

                if ($adminCommissionType == AdminCommissionType::PERCENTAGE) {
                    $adminServiceFee = ($total * ($adminCommission / 100));
                
                } else {
                    $adminServiceFee = $adminCommission;
                }

                $driverCommission = ($total - $adminServiceFee -$servicetaxamount) > 0 ? $total - $adminServiceFee - $servicetaxamount: 0;
                $basePrice = 0;
                $baseDistance = 0;
                $distancePrice = 0;
                $totalDistancePrice = 0;
                $timePrice = 0;
                $totalTimePrice = 0;
                $discount_amount = 0;
                $finalWaitingPrice = 0;
                $givendistance = 0;
                $out_of_zone_price = 0;
                $total_booking_price = 0;
            }


            // $amount = number_format($subTotal,2);
            // $num =   explode('.', $subTotal);
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
            //    $subTotal =  $num[0] . '.' . $num[1];
            // }

            $calculated_trip_price = (int) $subTotal;
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
            $subTotal = $quotient + $amount_to_add;


            if($close_trip == 1 && $request_detail->destination_type == "OPEN")
            {
                return [
                    'base_price'                => $request_detail->closed_trip_amount,
                    'base_distance'             => 0,
                    'price_per_distance'        => 0,
                    'distance_price'            => 0,
                    'price_per_time'            => 0,
                    'time_price'                => 0,
                    'promo_discount'            => 0,
                    'waiting_charge'            => 0,
                    'admin_commision'           => $adminServiceFee,
                    'driver_commision'          => $request_detail->closed_trip_amount - $adminServiceFee,
                    'total_amount'              => $request_detail->closed_trip_amount,
                    'sub_total'                 => 0,
                    'total_distance'            => $givendistance,
                    'service_tax'               => $servicetaxamount,
                    'service_tax_percentage'    => $servicetax,
                    'total_time'                => 0,
                    'requested_currency_code'   => $request_detail->requested_currency_code,
                    'requested_currency_symbol' => $request_detail->requested_currency_symbol,
                    'out_of_zone_price'         => $out_of_zone_price,
                    'booking_fees'              => 0
                ];
            
            }else {

                return [
                    'base_price'                => $basePrice,
                    'base_distance'             => $baseDistance,
                    'price_per_distance'        => $distancePrice,
                    'distance_price'            => $totalDistancePrice,
                    'price_per_time'            => $timePrice,
                    'time_price'                => $totalTimePrice,
                    'promo_discount'            => $discount_amount,
                    'waiting_charge'            => $finalWaitingPrice,
                    'admin_commision'           => $adminServiceFee,
                    'driver_commision'          => $driverCommission,
                    'total_amount'              => (float)$subTotal,
                    'sub_total'                 => $total,
                    'total_distance'            => $givendistance,
                    'service_tax'               => $servicetaxamount,
                    'service_tax_percentage'    => $servicetax,
                    'total_time'                => $totalTripTime,
                    'requested_currency_code'   => $request_detail->requested_currency_code,
                    'requested_currency_symbol' => $request_detail->requested_currency_symbol,
                    'out_of_zone_price'         => $out_of_zone_price,
                    'booking_fees'              => $total_booking_price
                ];
            }
            
        }
    }
}