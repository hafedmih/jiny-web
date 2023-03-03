<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\Promocode;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\User;

use App\Traits\CommanFunctions;
use DB;

class OutstationController extends BaseController
{
    use CommanFunctions;
    public function outstationList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);
            
            $outstation_list = OutstationMaster::where('status',1)->get();
            
            if(is_null($outstation_list)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{

                return $this->sendResponse('Data Found',$outstation_list,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function outstationEta(Request $request)
    {
        try{
            // token check 
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            // outstation Id 

            $id = $request->outstation_id;
            $outstation = OutstationMaster::where('status',1)->where('id',$id)->first();
            
            if(is_null($outstation)){
                return $this->sendError('No Data Found',[],404);  
            }
            if($request->has('pick_lat') && $request->has('pick_lng')){ 
                $pick_lat = $request->pick_lat;
                $pick_lng = $request->pick_lng;
            }
            else{
                $pick_lat = $outstation->pick_lat;
                $pick_lng = $outstation->pick_lng;
            }
            
            $OutstationPriceFixing = OutstationPriceFixing::join('vehicle', 'vehicle.id', '=', 'outstation_price_fixing.type_id')
            ->where('outstation_price_fixing.status', '1')
            ->where('vehicle.status','1')->orderBy('vehicle.sorting_order', 'ASC')->with('getVehicle')
            ->get(['outstation_price_fixing.*', 'vehicle.id']);
            
            $promo_code = '';
            if($request->has('promo_code') && $request->promo_code != ""){
                $promo_code = Promocode::where('promo_code',$request->promo_code)->where('status',1)->first();
                if(is_null($promo_code)){
                    return $this->sendError('Invalid Promo code',[],404);  
                }
            }

            $diff_in_hours = 0;
            if($request->has('from_date')){
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->to_date);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->from_date);
                $diff_in_hours = $to->diffInHours($from);
                if($diff_in_hours == 0)
                    $diff_in_hours = 3;
            }

            foreach ($OutstationPriceFixing as $key => $value) {
                $OutstationPriceFixing[$key]->master_id = $id;
                $trip_way_type = 'ONE';
                if($request->has('trip_way_type')){
                    $trip_way_type = $request->trip_way_type;
                }
                
                $distances = $this->getDistance($pick_lat,$pick_lng,$outstation->drop_lat,$outstation->drop_lng);
                // $distances = $outstation->distance;
                $OutstationPriceFixing[$key]->distance = $distances;
                if($trip_way_type == 'ONE')
                    $OutstationPriceFixing[$key]->admin_commission = $value->admin_commission_type == 1 ? ((($distances * 2) * $value->distance_price) * $value->admin_commission)/100 : $value->admin_commission;
                else
                    $OutstationPriceFixing[$key]->admin_commission = $value->admin_commission_type == 1 ? ((($distances * 2) * $value->distance_price_two_way) * $value->admin_commission)/100 : $value->admin_commission;

                $OutstationPriceFixing[$key]->currency_symbol = $outstation->getCountry->currency_symbol;

                $finalDistance = $distances * 2;
                $distance_cost = 0;
                $hillstation_price = 0;
                $driver_price = 0;
                $distances = $distances*2;

                if($trip_way_type == 'ONE'){
                    $distance_cost = ($distances ) * $value->distance_price;
                    if($finalDistance < $value->minimum_km)
                        $driver_price = $value->base_fare;
                    else
                        $driver_price = $value->driver_price;
                }else{
                    
                    $driverSinglePrice = $value->day_rent_two_way;
                   
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
                                
                                $driver_price = ($driverSinglePrice * floor($aa)) + $driverSinglePrice ;

                                $grace_time = $value->grace_time;
                                $waiting_charge = $value->waiting_charge;
                                if($remainder < $grace_time){
                                    $driver_price = +$driver_price;
                                }else{
                                    $waiting_time_charge = $grace_time * $waiting_charge;
                                    $driver_price  = $waiting_time_charge + $driver_price;
                                }
                                // dd($driver_price);
                            }

                        }
                    }
                    $distance_cost = ($distances ) * $value->distance_price_two_way;
                    
                }
               
                $OutstationPriceFixing[$key]->driver_price =  $driver_price;
                if($outstation->hill_station == 'YES'){
              
                    $hillstation_price =  $value->hill_station_price;
                    $OutstationPriceFixing->hill_station_price =  $value->hill_station_price;
                }else{
                    $OutstationPriceFixing->hill_station_price =  0;
                }
                
                // dd($hillstation_price);
                $total_amount = $distance_cost + $driver_price + $hillstation_price;

                $OutstationPriceFixing[$key]->total_amount = $total_amount;

                if($promo_code){
                    if(in_array($value->type_id,$promo_code->types)){
                        $use_request_count = RequestModel::where('user_id',$user->id)->where('promo_id',$promo_code->id)->where('is_completed',1)->count();
                        if($use_request_count < $promo_code->promo_user_reuse_count){
                            if($promo_code->select_offer_option == 4 && $promo_code->from_date <= date('Y-m-d') && $promo_code->to_date >= date('Y-m-d') || $promo_code->select_offer_option != 4){
                                if($promo_code->select_offer_option != 5 || $epromo_code->select_offer_option == 5 && $promo_code->user_id == $user->id){
                                    if(!$promo_code->trip_type || $promo_code->trip_type == 'RENTAL'){
                                        if($promo_code->target_amount < $total_amount){
                                            $amount1 = $promo_code->promo_type == 1 ? $promo_code->amount : $total_amount*$promo_code->percentage/100 ;
                                            $OutstationPriceFixing[$key]->promo_amount = number_format($amount1,2);
                                            $amount = $total_amount - $amount1;
                                            $OutstationPriceFixing[$key]->total_amount_promo = number_format($amount,2);
                                            $OutstationPriceFixing[$key]->promo_code = 1;
                                        }
                                        else{
                                            // $package_item_list[$key]->promo_amount = 0;
                                            // $package_item_list[$key]->total_amount_promo = $amount;
                                            $OutstationPriceFixing[$key]->promo_code = 1;
                                        }
                                    }
                                    else{
                                        $OutstationPriceFixing[$key]->promo_code = 0;
                                    }
                                }
                                else{
                                    $OutstationPriceFixing[$key]->promo_code = 0;
                                }
                            }
                            else{
                                $OutstationPriceFixing[$key]->promo_code = 0;
                            }
                        }
                        else{
                            $OutstationPriceFixing[$key]->promo_code = 0;
                        }
                    }
                    else{
                        $OutstationPriceFixing[$key]->promo_code = 0;
                    }
                }
                // else{
                //     $package_item_list[$key]->promo_code = 0;
                // }
            }
           
            return $this->sendResponse('Data Found',$OutstationPriceFixing,200);  
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
