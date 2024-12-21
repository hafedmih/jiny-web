<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use DB;
use Validator;

use App\Models\User;
use App\Models\taxi\Submaster;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use Carbon\Carbon;


class SubscriptionMasterController extends BaseController
{
    public function subscriptionList(Request $request)
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
            
            $subscription = Submaster::get();
            if(is_null($subscription)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data['subscription'] = $subscription;
                $data['currency_symbol'] = $user->getCountry ? $user->getCountry->currency_symbol : '';
                $data['profile_pic'] = $user->profile_pic;
                $data['user_name'] = $user->firstname." ".$user->lastname;
                $data['subscription_mode'] = $user->driver->subscription_type;
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function subscriptionAdd(Request $request)
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

            $validator = Validator::make($request->all(),[
                'subscription_id' => 'required'           
            ]);
            if($validator->fails()){
                return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
            }

            if($user->driver->subscription_type == 'COMMISSION'){
                return $this->sendError('Sorry! You are not subscription driver',[],404);
            }
            
            $old_subscription = DriverSubscriptions::where('user_id',$user->id)->where('to_date','>=',NOW())->count();

            if($old_subscription > 0){
                return $this->sendError('Sorry!, You are already subscriped.',[],404);
            }

            $subscription = Submaster::where('slug',$request->subscription_id)->first();

           // dd($subscription);
           if(!$subscription){
                return $this->sendError('Sorry!, Invalide subscriped.',[],404);
            }

            $wallet = Wallet::where('user_id',$user->id)->first();
            if(!$wallet){
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance_amount' => 0,
                    'earned_amount' => 0,
                    'amount_spent' => 0
                ]);
            }

            if($subscription->amount > $wallet->balance_amount){
                return $this->sendError('Sorry! low balaance in wallet.',[],404);
            }

            $driver_subcription = DriverSubscriptions::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'from_date' => NOW(),
                'to_date' => Carbon::now()->addDays($subscription->validity),
                'amount' => $subscription->amount,
                'paid_status' => 1
            ]);

            $user->active = true;
            $user->save();

            $wallet->balance_amount -= $subscription->amount;
            $wallet->earned_amount += $subscription->amount;
            $wallet->save();

            $wallet_transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $subscription->amount,
                'purpose' => 'wallet spant amount to subscription',
                'type' => 'SPENT',
                'user_id' => $user->id,
            ]);

            return $this->sendResponse('You are Subscriped completed',$driver_subcription,200);  
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
