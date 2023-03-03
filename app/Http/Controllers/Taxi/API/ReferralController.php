<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\User;
use App\Models\taxi\Referral;
use App\Models\taxi\Settings;
use App\Models\taxi\ReferalAmountList;



use DB;
use DateTime;

class ReferralController extends BaseController
{
    public function getReferraldriver(Request $request)
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
           
           if(!$user->hasRole('driver'))
                return $this->sendError('No Driver found',[],403);

            $referral = Referral::where('referred_by',$user->id)->get();
            $driver_referral_amount = ReferalAmountList::where('referal_user_id',$user->id)->sum('amount');

            $driver_driver_referal_amount = Settings::where('name','driver_driver_referal_amount')->first();
            $driver_user_referal_amount = Settings::where('name','driver_user_referal_amount')->first();

            $data['referral'] = $referral;
            $data['referral_code'] = $user->referral_code;
            $data['refer_by_driver_amount'] = $driver_driver_referal_amount ? (int)$driver_driver_referal_amount->value : 0;
            $data['refer_by_user_amount'] = $driver_user_referal_amount ? (int)$driver_user_referal_amount->value : 0;
            $data['referral_amount'] = (int)$driver_referral_amount;
            $data['currency_symbol'] = $user->getCountry ? $user->getCountry->currency_symbol : '';
            
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function getReferraluser(Request $request)
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
           
           if(!$user->hasRole('user'))
                return $this->sendError('No User found',[],403);

            $referral = Referral::where('referred_by',$user->id)->get();
            $user_referral_amount = ReferalAmountList::where('referal_user_id',$user->id)->sum('amount');

            $user_driver_referal_amount = Settings::where('name','user_driver_referal_amount')->first();
            $user_user_referal_amount = Settings::where('name','user_user_referal_amount')->first();

            $data['referral'] = $referral;
            $data['referral_code'] = $user->referral_code;
            $data['refer_by_driver_amount'] = $user_driver_referal_amount ? (int)$user_driver_referal_amount->value : 0;
            $data['refer_by_user_amount'] = $user_user_referal_amount ? (int)$user_user_referal_amount->value : 0;
            $data['referral_amount'] = (int)$user_referral_amount;
            $data['currency_symbol'] = $user->getCountry ? $user->getCountry->currency_symbol : '';

            
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


  
   

   
}
