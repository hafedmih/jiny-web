<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\RequestRating;
use App\Models\User;
use App\Models\taxi\Vehicle;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestBill;
use App\Models\taxi\Settings;
use App\Models\taxi\Wallet;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\Requests\RequestHistory;
use App\Models\taxi\DriverDocument;
use Illuminate\Support\Carbon;

use DB;
use DateTime;

class RequestInProgressController extends BaseController
{
    public function userRequestInProgressController(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
                // dd($request);
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);
           
           if(!$user->hasRole('user'))
                return $this->sendError('No User found',[],403);

            $driver_search_radius_get = Settings::where('name','driver_search_radius')->first();

            $driver_search_radius = $driver_search_radius_get ? $driver_search_radius_get->value : '';
                // dd($user->slug);
            // $data = new \stdClass();
            $data['user']['slug'] = $user->slug;
            $data['user']['firstname'] = $user->firstname;
            $data['user']['lastname'] = $user->lastname;
            $data['user']['email'] = $user->email;
            $data['user']['phone_number'] = $user->phone_number;
            $data['user']['referral_code'] = $user->referral_code;
            $data['user']['profile_pic'] = $user->profile_pic;
            $data['user']['country'] = $user->getCountry ? $user->getCountry->name : '';
            $data['user']['dial_code'] = $user->getCountry ? $user->getCountry->dial_code : '';
            $data['user']['country_code'] = $user->getCountry ? $user->getCountry->code : '';
            $data['user']['currency_code'] = $user->getCountry ? $user->getCountry->currency_code : '';
            $data['user']['currency_symbol'] = $user->getCountry ? $user->getCountry->currency_symbol : '';
            $data['user']['search_radius'] = (float)$driver_search_radius;

            $request_detail = RequestModel::where('user_id',$user->id)->where('driver_rated',0)->where('is_cancelled',0)->orderBy('trip_start_time','asc')->first();
            if ($request_detail) {
                if($request_detail->is_completed == 0){
                    $request_result =  fractal($request_detail, new TripRequestTransformer);
                    $data['trips'] = $request_result;
                }else{
                    $request_bill = RequestBill::where('request_id',$request_detail->id)->first();
                    if(is_null($request_bill)){
                        $request_result =  fractal($request_detail, new TripRequestTransformer);
                        $data['trips'] = $request_result;
                       
                    }else{
                        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
                        $data['trips'] = $request_result;
                    }
                   
                }
            }
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function driverRequestInProgressController(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);

           // dd($request);

          
           if(!$user->hasRole('driver'))
                return $this->sendError('No Driver found',[],403);

            //dd($driver_document);

            $customer_care_number = Settings::where('name','customer_care_number')->first();
            $head_office_number = Settings::where('name','head_office_number')->first();
            $auto_araive_radius_pickup = Settings::where('name','auto_araive_radius_pickup')->first();
            $auto_araive_radius_drop = Settings::where('name','auto_araive_radius_drop')->first();

            $customer_care_number = $customer_care_number ? $customer_care_number->value : "";
            $auto_araive_radius_pickup = $auto_araive_radius_pickup ? $auto_araive_radius_pickup->value : "";
            $auto_araive_radius_drop = $auto_araive_radius_drop ? $auto_araive_radius_drop->value : "";


            $today_request_completed = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',date('Y-m-d'))->where('is_completed',1)->count();
            $today_request_cancelled = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',date('Y-m-d'))->where('is_cancelled',1)->count();
            $today_earnings = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',date('Y-m-d'))->join('request_bills', 'requests.id', '=', 'request_bills.request_id')->sum('driver_commision');
            
            $driver_detail = RequestModel::where('driver_id',$user->id)->orderby('trip_start_time','desc')->first();

            
            $driver_wallet_balance = Wallet::where('user_id','=',$user->id)->first();
            $data['head_office_number'] = $head_office_number;
            $data['customer_care_number'] = $customer_care_number;
            $data['auto_araive_radius_pickup'] = $auto_araive_radius_pickup;
            $data['auto_araive_radius_drop'] = $auto_araive_radius_drop;
            
            // $data = new \stdClass();
            $data['driver']['slug'] = $user->slug;
            $data['driver']['firstname'] = $user->firstname;
            $data['driver']['lastname'] = $user->lastname;
            $data['driver']['email'] = $user->email;
            $data['driver']['referral_code'] = $user->referral_code;
            $data['driver']['phone_number'] = $user->phone_number;
            $data['driver']['profile_pic'] = $user->profile_pic;
            $data['driver']['type_id'] = $user->driver->vehicletype->slug;
            $data['driver']['image'] = $user->driver->vehicletype->image;
            $data['driver']['type_name'] = $user->driver->vehicletype ? $user->driver->vehicletype->vehicle_name : '';
            $data['driver']['country'] = $user->getCountry ? $user->getCountry->name : '';
            $data['driver']['dial_code'] = $user->getCountry ? $user->getCountry->dial_code : '';
            $data['driver']['country_code'] = $user->getCountry ? $user->getCountry->code : '';
            $data['driver']['currency_code'] = $user->getCountry ? $user->getCountry->currency_code : '';
            $data['driver']['currency_symbol'] = $user->getCountry ? $user->getCountry->currency_symbol : '';
            $data['driver']['online'] = $user->online_by;
            $data['driver']['approve_status'] = $user->active;
            $data['driver']['document_upload_status'] = $user->driver->document_upload_status;
            $data['driver']['service_category'] =$user->driver->service_category;
            $data['driver']['today_completed'] = $today_request_completed;
            $data['driver']['today_cancelled'] = $today_request_cancelled;
            $data['driver']['wallet_amount'] = $driver_wallet_balance ? $driver_wallet_balance->balance_amount: 0;
            $data['driver']['today_earnings'] = $today_earnings;
        

            $subscription = DriverSubscriptions::where('user_id',$user->id)->where('from_date','<=',NOW())->where('to_date','>=',NOW())->first();

            if($user->driver->subscription_type == "COMMISSION"){
                $status = 1;
            }
            elseif($user->driver->subscription_type == "SUBSCRIPTION"){
                $status = 2;
                $data['driver']['subscription_status'] = $subscription ? true : false;
            }
            else{
                $status = 3;
                $data['driver']['subscription_status'] = $subscription ? true : false;
            }

            $data['driver']['subscription_type'] = $status;
            $data['driver']['block_reson'] = $user->block_reson;

            if($subscription){

                $datetime1 = new DateTime(NOW());
                $datetime2 = new DateTime($subscription->to_date);
                $interval = $datetime1->diff($datetime2);
                $days = $interval->format('%a');

                $data['driver']['subscription']['total_days'] = $subscription->subscriptionDetails->validity;
                $data['driver']['subscription']['balance_days'] = $days;
                $data['driver']['subscription']['from_date'] = $subscription->from_date;
                $data['driver']['subscription']['to_date'] = $subscription->to_date;
                $data['driver']['subscription']['paid_status'] = $subscription->paid_status;
                $data['driver']['subscription']['amount'] = $subscription->amount;
                $data['driver']['subscription']['name'] = $subscription->subscriptionDetails->name;
            }
            else{
                $data['driver']['subscription'] = null;
            }

                // dd($user->id);
            $one_hour = Carbon::now()->addMinutes(15);
            $request_detail = RequestModel::where('driver_id',$user->id)->where('is_cancelled',0)->where('is_driver_started',1)->orderby('trip_start_time','desc')->first();
            // dump($one_hour);
            // dd($request_detail);
            
            if ($request_detail) {
                if($request_detail->location_approve){
                    $location_history = RequestHistory::where('request_id',$request_detail->id)->where('olat',null)->first();
                  
                    if(!is_null($location_history)){
                        $data['driver']['changed_location_adddress'] = $location_history->drop_address;
                        $data['driver']['changed_location_lat'] = $location_history->dlat;
                        $data['driver']['changed_location_long'] = $location_history->dlng;
                        $data['driver']['changed_location_time'] = $location_history->created_at;
                        $data['driver']['changed_location_id'] = $location_history->id;
                    }
                }
                
                if($request_detail->is_completed == 0){
                    
                    $request_result =  fractal($request_detail, new TripRequestTransformer);
                    // if($one_hour < NOW()){
                        $data['trips'] = $request_result;
                    // }
                    // else{
                    //     $data['trips'] = null;
                    // }
                    
                }else{
                    if($request_detail->is_instant_trip == 1 ){
                      
                    }else{
                        $rating = RequestRating::where('request_id',$request_detail->id)->where('user_id',$user->id)->first();
                        
                        if(is_null($rating)){
                                $request_bill = RequestBill::where('request_id',$request_detail->id)->first();
                            if(is_null($request_bill)){
                                $request_result =  fractal($request_detail, new TripRequestTransformer);
                                $data['trips'] = $request_result;
                            }else{
                                $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
                                $data['trips'] = $request_result;
                            }
                        }
                        
                     }
                    
                }
            }

            $request = RequestMeta::where('driver_id',$user->id)->where('active',1)->first();
            // dd($request);
            // dd($request->request_id);
            if(is_null($request)){
                $request_detail = RequestModel::where('driver_id',$user->id)->where('is_cancelled',0)->where('is_completed',0)->where('is_driver_started',0)->first();
                if(!is_null($request_detail)){
                    $result = fractal($request_detail, new TripRequestTransformer);
                    $data['driver']['meta']= $result;
                }
                

            }else{
                
                $request_detail = RequestModel::where('id',$request->request_id)->first();
                // dd($request_detail );
                $result = fractal($request_detail, new TripRequestTransformer);
                $data['driver']['meta']= $result;
            }
           

            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
