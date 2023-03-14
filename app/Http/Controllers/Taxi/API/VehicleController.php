<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Taxi\API\GetTypesRequest;

use App\Models\taxi\Vehicle;
use App\Models\taxi\VehicleModel;
use App\Models\taxi\Promocode;
use App\Models\taxi\Settings;
use App\Models\taxi\Outofzone;
use App\Models\taxi\Requests\Request as RequestModel;

use App\Models\taxi\Zone;
use App\Models\User;

use App\Traits\CommanFunctions;

use DB;
use File;
use Validator;

class VehicleController extends BaseController
{
    use CommanFunctions;

    public function typeList(Request $request)
    {
        try{
            $vehicle = Vehicle::join('vehicle_model','vehicle.id','=','vehicle_model.vehicle_id')->select('vehicle.*')->where('vehicle.status',1)->groupby('vehicle_model.vehicle_id')->orderBy('vehicle.sorting_order', 'ASC')->get();
            if(is_null($vehicle))
                return $this->sendError('No Data Found',[],404);  

                $data = [];

                if(!is_null($vehicle)){
                    // foreach($vehicle as $key => $value)
                        $data['types'] = $vehicle;
                        return $this->sendResponse('Data Found',$data,200);
                }
                return $this->sendError('No Data Found',[],404);
            }  catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Catch error','failure.'.$e,400);  
            }
    }


    public function getTypes(GetTypesRequest $request)
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
            $data = $request->all();

            // get zone use pickup lat and long
            $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);
            if(is_null($zone))
                return $this->sendError('Non services zone',[],404);
            if($zone->non_service_zone == 'Yes')
                return $this->sendError('Non services zone',[],404);

            $distance = 0;
            //if stops calculate the distance
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
                // get distance for pickup lat long to drop lat long
               // $distance = $this->getDistance($data['pickup_lat'],$data['pickup_long'],$data['drop_lat'],$data['drop_long']);
              
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
            $count = [];
            foreach ($zone->getZonePrice as $key => $value) {
                
                $zonePrice = (object) [
                    'type_name' => $value->getType->vehicle_name,
                    'type_slug' => $value->getType->slug,
                    'capacity' => $value->getType->capacity,
                    'category' => $value->getType->getCategory ? $value->getType->getCategory->category_name : '',
                    'type_image' => $value->getType->image,
                    'type_image_select' => $value->getType->highlight_image,
                    'sorting_order' => $value->getType->sorting_order,
                ];

                // $drop_zone = $this->getZone($data['drop_lat'],$data['drop_long']);
                $drop_zone =0 ;
                $outofzonefee = 0;
                if(!$drop_zone){                  
                    $int_distance = (int)$distance;
                    
                    $outofzone = Outofzone::orderby('id','desc')->get();

                    foreach($outofzone as $out){
                        if($out->kilometer >= $int_distance){
                            $outofzonefee = $out->price;
                        }
                    }
                    // $outofzone = Outofzone::where('kilometer','>=',$int_distance)->orderby('id','desc')->first();
                
                    // if($outofzone){
                    //     $outofzonefee = $outofzone->price;
                    // }
                    // else{
                    //     $outofzone = Outofzone::orderby('id','desc')->first();
                    //     $outofzonefee = $outofzone->price;
                    // }
                }else{
                    if($drop_zone->non_service_zone == 'Yes'){
                        $int_distance = (int)$distance;
                        $outofzone = Outofzone::orderby('id','desc')->get();

                        foreach($outofzone as $out){
                            if($out->kilometer >= $int_distance){
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
               //Ride Now
                if($data['ride_type'] == "RIDE_NOW"){

                    $totalvalue = $this->etaCalculation($distance,$value->ridenow_base_distance,$value->ridenow_base_price,$value->ridenow_price_per_distance,$value->ridenow_booking_base_fare,$value->ridenow_booking_base_per_kilometer,$outofzonefee);
                }
                // Ride Later
                else if($data['ride_type'] == "RIDE_LATER"){

                    $totalvalue = $this->etaCalculation($distance,$value->ridelater_base_distance,$value->ridelater_base_price,$value->ridelater_price_per_distance,$value->ridelater_booking_base_fare,$value->ridelater_booking_base_per_kilometer,$outofzonefee);
                }

                $total_amount = $totalvalue['sub_total'];
                if (request()->has('promo_code')) { 
                    $expired = Promocode::whereStatus(true)->where('promo_code', $request['promo_code'])->first();
                    $total_amount = str_replace(',', '', $total_amount);
                    // dump((double) $total_amount);
                    // dump((double) $expired['target_amount']);
                    if ($expired && $expired['promo_code'] == $request['promo_code']) {
                        if(in_array($value->getType->id,$expired->types)){
                        $request_count = RequestModel::where('user_id',$user->id)->where('promo_id',$expired['id'])->where('is_completed',1)->count();
                            if($expired['select_offer_option'] == 4 && $expired['from_date'] <= date('Y-m-d') && $expired['to_date'] >= date('Y-m-d') || $expired['select_offer_option'] != 4 || $expired['select_offer_option'] == 1 && $request_count >= $expired['new_user_count']){
                                if($expired['select_offer_option'] != 5 || $expired['select_offer_option'] == 5 && $expired['user_id'] == $user->id){
                                    if(!$expired['trip_type'] || $expired['trip_type'] == 'LOCAL'){
                                        if((double) $total_amount >= (double) $expired['target_amount']){
                                            $zonePrice->promo_code = 1;
                                            $total_amounts = $this->promoCalculation($expired,$total_amount);
                        
                                            $zonePrice->promo_total_amount = $total_amounts;
                                            $total_amounts = str_replace(',', '', $total_amounts);
                                            $amounts = (double) $total_amount - (double) $total_amounts;
                                            $zonePrice->promo_amount = number_format($amounts,2);
                                        }
                                        else{
                                            $zonePrice->promo_code = 1;
                                        }
                                    }
                                    else{
                                        $zonePrice->promo_code = 0;
                                    }
                                }
                                else{
                                    $zonePrice->promo_code = 0;
                                }
                            }
                            else{
                                $zonePrice->promo_code = 0;
                            }
                        }
                        else{
                            $zonePrice->promo_code = 0;
                        }
                    } 
                    else  
                    {
                        $zonePrice->promo_code = 0;
                    }    
                }
                $zonePrice->base_price = $value->ridenow_base_price;
                $zonePrice->base_distance = $value->ridenow_base_distance;
                $zonePrice->distance = $distance;
                $zonePrice->total_amount = $total_amount;
                $zonePrice->free_waiting_time = $value->ridenow_free_waiting_time;
                $zonePrice->waiting_charge = $value->ridenow_waiting_charge;
                $zonePrice->price_per_time = $value->ridenow_price_per_time;

                $computedDistance = $distance - $value->ridenow_base_distance;
                if($computedDistance >= 0 ){
                    $zonePrice->computed_price = $value->ridenow_price_per_distance * $computedDistance;
                    $zonePrice->computed_distance = $computedDistance;
                }
                $zonePrice->price_per_distance = $value->ridenow_price_per_distance;
                $zonePrice->booking_base_fare = $value->ridenow_booking_base_fare;
                $zonePrice->booking_base_per_kilometer = $value->ridenow_booking_base_per_kilometer;
                $zonePrice->booking_fees = $totalvalue['booking_fee'];
                $zonePrice->out_of_zone_price = $totalvalue['outofzonefee'];
                // set surge price
                foreach($value->getSurgePrice as $key1 => $value1){
                    // dd($value1);
                    if($value1->start_time <= $data['ride_time'] && $value1->end_time >= $data['ride_time'] && in_array(date("l",strtotime($data['ride_date'])),explode(',',$value1->available_days))){
                        // dd($distance);
                        // $total_amount = $distance * $value1->surge_price;
                        $total_amount = ($value1->surge_distance_price * $distance) + $value1->surge_price + $zonePrice->waiting_charge + $zonePrice->booking_fees;
                        $zonePrice->base_price = $value1->surge_price;
                        $final_distance =  $distance - $zonePrice->base_distance;
                        $zonePrice->price_per_distance = $value1->surge_distance_price;
                        $zonePrice->computed_price = $value1->surge_distance_price * $final_distance;
                        $zonePrice->distance = $distance;
                        $zonePrice->total_amount = $total_amount;
                    }
                }
                if($totalvalue['outofzonefee'] > 0){
                    if($zonePrice->type_slug != "bajaj-auto"){
                        $count[$key] = $value->getType->sorting_order;
                        array_push($zone_price, $zonePrice);
                    }
                }
                else{
                    $count[$key] = $value->getType->sorting_order;
                    array_push($zone_price, $zonePrice);
                }

            }
            
            $dd = array_multisort($count, SORT_ASC, $zone_price);
            // dd($zone_price);
            $datas->zone_type_price = $zone_price;

            DB::commit();
            return $this->sendResponse('Data Found',$datas,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function vehicleModelList(Request $request)
    {
        try{
            $vehicle = Vehicle::where('slug',$request->slug)->first();
            $vehicle_model = VehicleModel::where('vehicle_id',$vehicle->id)->get();
            if(is_null($vehicle))
                return $this->sendError('No Data Found',[],404); 

                foreach ($vehicle_model as $key => $value) {
                    //$data['vehiclemodel'][$key]['vehicle_name'] = $value->getVehicle->vehicle_name;
                    $data['vehiclemodel'][$key]['model_name'] = $value->model_name;
                    $data['vehiclemodel'][$key]['slug'] = $value->slug;
                    //$data['vehiclemodel'][$key]['description'] = $value->description;
                    //$data['vehiclemodel'][$key]['image'] = $value->image;
                }
                    return $this->sendResponse('Data Found',$data,200); 
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }



}
