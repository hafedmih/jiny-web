<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Vehicle;
use App\Models\taxi\PackageItem;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\Promocode;
use App\Models\User;

use DB;

class PackageController extends BaseController
{
    public function packageList(Request $request)
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
            
            $package_list = PackageMaster::where('status',1)->get();
            
            if(is_null($package_list)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                foreach ($package_list as $key => $value) {
                    $package_list[$key]->currency_symbol = $value->getCountry ? $value->getCountry->currency_symbol : null;
                }
                return $this->sendResponse('Data Found',$package_list,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function packageEta(Request $request)
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
            
            $package = $request->package_id;
             $package_list = PackageMaster::with('getPackageItems','getPackageItems.getVehicle')->where('status',1)->where('slug',$package)->first();

            if(is_null($package_list)){
                return $this->sendError('No Data Found',[],404);   
            }
            else{
                $package_item_list = $package_list->getPackageItems;
                if(is_null($package_item_list)){
                    return $this->sendError('No Data Found',[],404);  
                }
                $promo_code = '';
                if($request->has('promo_code') && $request->promo_code != ""){
                    $promo_code = Promocode::where('promo_code',$request->promo_code)->where('status',1)->first();
                    if(is_null($promo_code)){
                        return $this->sendError('Invalid Promo code',[],404);  
                    }
                }

                // dd($package_item_list);
                foreach ($package_item_list as $key => $value) {
                    $amount = $value->price + $package_list->driver_price;
                    $package_item_list[$key]->total_amount = $amount;
                    // $package_item_list[$key]->discount_amount = $promo_code->target_amount;
                    if($promo_code){
                        if(in_array($value->type_id,$promo_code->types)){
                            if($promo_code->select_offer_option == 4 && $promo_code->from_date <= date('Y-m-d') && $promo_code->to_date >= date('Y-m-d') || $promo_code->select_offer_option != 4){
                                if($promo_code->select_offer_option != 5 || $promo_code->select_offer_option == 5 && $promo_code->user_id == $user->id){
                                    if(!$promo_code->trip_type || $promo_code->trip_type == 'RENTAL'){
                                        if($promo_code->target_amount < $amount){
                                            $package_item_list[$key]->promo_amount = $promo_code->promo_type == 1 ? $promo_code->amount : $amount*$promo_code->percentage/100 ;
                                            $amount = $amount - ($promo_code->promo_type == 1 ? $promo_code->amount : $amount*$promo_code->percentage/100 );
                                            $package_item_list[$key]->total_amount_promo = $amount;
                                            $package_item_list[$key]->promo_code = 1;
                                        }
                                        else{
                                            // $package_item_list[$key]->promo_amount = 0;
                                            // $package_item_list[$key]->total_amount_promo = $amount;
                                            $package_item_list[$key]->promo_code = 1;
                                        }
                                    }
                                    else{
                                        $package_item_list[$key]->promo_code = 0;
                                    }
                                }
                                else{
                                    $package_item_list[$key]->promo_code = 0;
                                }
                            }
                            else{
                                $package_item_list[$key]->promo_code = 0;
                            }
                        }
                        else{
                            $package_item_list[$key]->promo_code = 0;
                        }
                    }
                    // else{
                    //     $package_item_list[$key]->promo_code = 0;
                    // }
                    $package_item_list[$key]->currency_symbol = $package_list->getCountry ? $package_list->getCountry->currency_symbol : null;
                    $package_item_list[$key]->order_by = $value->getVehicle->sorting_order;
                }
                  $package_item_list1 = $package_item_list->sortBy('order_by')->values()->all();
               
                return $this->sendResponse('Data Found',$package_item_list1,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
