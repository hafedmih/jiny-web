<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\boilerplate\OauthClients;
use App\Models\taxi\Driver;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Traits\RandomHelper;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Settings;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Wallet;
use Illuminate\Support\Facades\Storage;
use App\Traits\CommanFunctions;
use App\Constants\PushEnum;
use App\Jobs\SendPushNotification;
use DB;
use File;
use Validator;
use Carbon\Carbon;

class MoneyBackController extends BaseController
{
    use CommanFunctions, RandomHelper;
  
    public function moneyBack(Request $request)
    {
        try{
            $client_login = $this::getCurrentClient(request());
      
            if(is_null($client_login)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($client_login->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);

            if(!$user->hasRole('driver'))
                return $this->sendError('No Driver found',[],403);

                $validator = Validator::make($request->all(), [
                    'request_id' => 'required',
                    'amount' => 'required',
                ]);
           
                if($validator->fails()){
                    return $this->sendError('Validation Error',$validator->errors(),412);       
                }

                $trip_details = RequestModel::where('id',$request->request_id)->where('is_completed',1)->first();
                if($trip_details){
                    $driver_wallet = Wallet::where('user_id',$trip_details->driver_id)->first();
                    $user_wallet = Wallet::where('user_id',$trip_details->user_id)->first();
                   
                   
                    if($driver_wallet->balance_amount > $request->amount){
                        /** Driver Wallet */
                        $driver_wallet->amount_spent += $request['amount'];
                        $driver_wallet->balance_amount -= $request['amount'];
                        $driver_wallet->save();

                        $wallet_transaction = WalletTransaction::create([
                            'wallet_id' => $driver_wallet->id,
                            'amount' =>  $request['amount'],
                            'purpose' => 'Balance amount transfer successfully',
                            'type' => 'SPENT',
                            'user_id' => $trip_details->driver_id,
                            'request_id' => $trip_details->id
                        ]);

                        /** User wallet */
                        if(is_null($user_wallet)){
                            $user_wallet = new Wallet();
                            $user_wallet->user_id = $trip_details->user_id;
                            $user_wallet->earned_amount = $request['amount'];
                            $user_wallet->balance_amount = $request['amount'];
                            $user_wallet->save();
                        }else{
                            $user_wallet->earned_amount	+= $request['amount'];
                            $user_wallet->balance_amount += $request['amount'];
                            $user_wallet->save();
                        }
                        

                        $wallet_transaction = WalletTransaction::create([
                            'wallet_id' => $user_wallet->id,
                            'amount' =>  $request['amount'],
                            'purpose' => 'Balance amount added successfully',
                            'type' => 'EARNED',
                            'user_id' => $trip_details->user_id,
                            'request_id' => $trip_details->id
                        ]);
                        $rider = User::where('id',$trip_details->user_id)->first();
                        if ($rider) {
                            $title = Null;
                            $body = '';
                            $lang = $rider->language;
                            $push_data = $this->pushlanguage($lang,'user-trip-balance-amount');
                            if(is_null($push_data)){
                                $title     = "Your account has been credited with ". $request['amount'];
                                $body      = "Your account has been credited with ". $request['amount'];
                                $sub_title = "Your account has been credited with ". $request['amount'];
                            }else{
                                $title     =  $push_data->title;
                                $body      =  $push_data->description;
                                $sub_title =  $push_data->description;
            
                            }   
                            
                           // @ TODO User get push and socket
            
                            // Form a socket sturcture using users'id and message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message  = PushEnum::MONEY_BACK;
                            $socket_data->moneyBackAmount  = (double)$request['amount'];
                            $socket_data->currency_symbol  = $trip_details->requested_currency_symbol;

                            $socketData = ['event' => 'money_back_'.$rider->slug,'message' => $socket_data];
                            
                            sendSocketData($socketData);
            
                            $pushData = ['notification_enum' => PushEnum::MONEY_BACK];
                            dispatch(new SendPushNotification($title,$pushData, $rider->device_info_hash, $rider->mobile_application_type,0,$sub_title));
                        }
                        $trip_details->money_back_amount = $request['amount'];
                        $trip_details->save();
                    }else{
                        return $this->sendError('insufficient balance.',[],403);
                    }
                }else{
                    return $this->sendError('No data found',[],403);
                }
          
            DB::commit();
            return $this->sendResponse('Your money transfer has been successfully completed.',[],200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

}