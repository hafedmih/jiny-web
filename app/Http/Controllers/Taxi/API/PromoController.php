<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Promocode;
use App\Models\boilerplate\Country;
use DB;
use App\Models\User;
use File;
use Validator;
use App\Constants\Promo;
use App\Models\taxi\Requests\Request as RequestModel;


class PromoController extends BaseController
{
    public function PromoList(Request $request)
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

            $promocode = Promocode::where('status',1);
            // $promocode = Promocode::where('status',1)->where('select_offer_option','!=',4)->orWhere([
            //     ['select_offer_option','=',4],['from_date','<=',date('Y-m-d')],['to_date','>=',date('Y-m-d')],['status','=',1]
            // ]);
            
            // $requests = RequestModel::where('user_id',$user->id)->where('is_completed',1)->count();
            
            // if($requests > 0)
            //     $promocode = $promocode->where('select_offer_option','!=',1);

            $promocode = $promocode->get();

            $promocountry = Promocode::where('status',1)->first();

            if(is_null($promocode)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $promocodes = [];
                foreach ($promocode as $key => $promolist){
                    switch ($promolist->select_offer_option) {
                        case 1: 
                            $promolist->select_offer_option_title = Promo::NewUserPromo;
                            $promolist->country_symbol = $promocountry->zone->GetCountry->currency_symbol;
                            $promolist->percentage = (int)$promolist->percentage;
                            break;
                        case 2: 
                            $promolist->select_offer_option_title = Promo::DistancePromo;
                            $promolist->country_symbol = $promocountry->zone->GetCountry->currency_symbol;
                            $promolist->percentage = (int)$promolist->percentage;
                            break;
                        case 3: 
                            $promolist->select_offer_option_title = Promo::AmountPromo;
                            $promolist->country_symbol = $promocountry->zone->GetCountry->currency_symbol;
                            $promolist->percentage = (int)$promolist->percentage;
                            break;
                        case 4: 
                            $promolist->select_offer_option_title = Promo::FestivalPromo;
                            $promolist->country_symbol = $promocountry->zone->GetCountry->currency_symbol;
                            $promolist->percentage = (int)$promolist->percentage;
                            break;
                        case 5: 
                            $promolist->select_offer_option_title = Promo::IndividualPromo;
                            $promolist->country_symbol = $promocountry->zone->GetCountry->currency_symbol;
                            $promolist->percentage = (int)$promolist->percentage;
                            break;
                    }
                    if($promolist->select_offer_option == 4 && $promolist->from_date <= date('Y-m-d') && $promolist->to_date >= date('Y-m-d') || $promolist->select_offer_option != 4){
                        $use_request_count = RequestModel::where('user_id',$user->id)->where('promo_id',$promolist->id)->where('is_completed',1)->count();
                        if($use_request_count < $promolist->promo_user_reuse_count || $promolist->select_offer_option == 1){
                            if($use_request_count < $promolist->new_user_count && $user->created_at > $promolist->created_at && $promolist->select_offer_option == 1 || $promolist->select_offer_option != 1){
                                if($promolist->user_id && in_array($user->id,$promolist->UsersList) && $promolist->select_offer_option == 5 || $promolist->select_offer_option != 5){
                                    if(in_array($request->type_id,$promolist->types)){
                                        array_push($promocodes, $promolist);
                                    }
                                }
                            }
                        }
                    }

                    
                }
                $data = new \stdClass();
                $data->promocode = $promocodes;
                // dd($data['promocode']);
                
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function PromoApply(Request $request)
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

            $countrydetails = Country::find($user->country_code);
            if(is_null($countrydetails))
                return $this->sendError('No country details found',[],404);

            $promo_check = Promocode::whereStatus(true)->where('promo_code', $request->promo_code)->first();
            // dd($promo_check);               

            if (!$promo_check) {
                return $this->sendError('Provided promocode invalid or expired',[],403);
            }              
            else  
            {
                // $requests = RequestModel::where('user_id',$user->id)->where('is_completed',1)->count();
            
                // if($requests > 0 && $promo_check->select_offer_option == 1)
                //     return $this->sendError('Provided promocode invalid or expired',[],403);
                if($promo_check->select_offer_option == 4 && $promo_check->from_date > date('Y-m-d') && $promo_check->to_date < date('Y-m-d'))
                    return $this->sendError('Provided promocode invalid or expired',[],403);
                
                if($promo_check->select_offer_option == 5 && !$promo_check->user_id && !in_array($user->id,$promo_check->UsersList))
                    return $this->sendError('Provided promocode invalid or expired',[],403);
                
                if(!in_array($request->type_id,$promo_check->types))
                    return $this->sendError('Provided promocode invalid or expired',[],403);
                
                // if($promo_check->trip_type != $request->trip_type)
                //     return $this->sendError('Provided promocode invalid or expired',[],403);

                $use_request_count = RequestModel::where('user_id',$user->id)->where('promo_id',$promo_check->id)->where('is_completed',1)->count();

                if($promo_check->select_offer_option == 1 && $use_request_count >= $promo_check->new_user_count)
                    return $this->sendError('Your Limited Exit',[],403);

                if($use_request_count >= $promo_check->promo_user_reuse_count && $promo_check->select_offer_option != 1)
                    return $this->sendError('Your Limited Exit',[],403);

                $use_request_total_count = RequestModel::where('promo_id',$promo_check->id)->where('is_completed',1)->count();
    
                if($use_request_total_count >= $promo_check->promo_use_count && $promo_check->select_offer_option != 1)
                    return $this->sendError('This Promocode Limited Exit',[],403);

                $data['user']['promocode_apply'] = "Promo Applied Successfully";
                $data['user']['currency'] = $countrydetails->currency_symbol;
                $data['user']['amount'] = $promo_check->amount;
                $data['user']['promo_type'] = $promo_check->promo_type;
                $data['user']['percentage'] = (int)$promo_check->percentage;
                return $this->sendResponse('Data Found',$data,200);  
            }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Catch error','failure.'.$e,400);  
            }


    }
}
