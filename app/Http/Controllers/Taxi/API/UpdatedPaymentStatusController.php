<?php

namespace App\Http\Controllers\Taxi\API;

use App\Constants\PushEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Requests\RequestPlace;
use App\Models\taxi\UpdatePaymentStatus;
use App\Traits\CommanFunctions;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use DB;
use App\Models\User;
use File;
use Validator;
use Razorpay\Api\Api;



class UpdatedPaymentStatusController extends BaseController
{
    use CommanFunctions;

    public function UpdatePaymentStatus(Request $request)
    {
       
        $validator = Validator::make($request->all(),[
            'request_id' => 'required',    
            'payment_id' => 'required', // CASH,CARD,WALLET
            'amount'     => 'required', 

        ]);
        if($validator->fails()){
            return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
        }

        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);

            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);


               $getrequest = RequestModel::where('id',$request['request_id'])->where('is_paid',0)->first();

               if(is_null($getrequest)){
                 return $this->sendError('No Request Found',[],404);  
               }

               $getrequest->is_paid = 1;
               $getrequest->update();

               $this->walletTransaction($request['amount'],$getrequest->driver_id,'EARNED','Trip Amount',$request['request_id']);

               $updatepayment = new UpdatePaymentStatus();
               $updatepayment->request_id = $request['request_id'];
               $updatepayment->user_id = $user->id;
               $updatepayment->amount = $request['amount'];
               $updatepayment->payment_id = $request['payment_id'];
               $updatepayment->is_paid = 1;
               $updatepayment->save();

               $response['payment_status'] = $getrequest;

                DB::commit();

                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'user-payment-done');
                    if(is_null($push_data)){
                        $title     = "Payment Success";
                        $body      = "Payment Success";
                        $sub_title = "User Payment Suceess";

                    }else{
                        $title     =  $push_data->title;
                        $body      =  $push_data->description;
                        $sub_title =  $push_data->description;

                    }   
                    
                   // @ TODO User get push and socket

                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::USER_PAYMENT_DONE;
                    $socket_data->result = ['is_paid' => true];
        
                    $socketData = ['event' => 'payment_done_'.$user->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::USER_PAYMENT_DONE];
                    dispatch(new SendPushNotification($title, $sub_title,$pushData, $user->device_info_hash, $user->mobile_application_type,0));


                    // @ TODO Driver push and socket

                    $get_driverrequest = RequestModel::where('id',$request['request_id'])->with('userDetail')->first();

                     // Form a socket sturcture using users'id and message with event name
                     $socket_data = new \stdClass();
                     $socket_data->success = true;
                     $socket_data->success_message  = PushEnum::PAYMENT_CHANGE;
                     $socket_data->result = ['is_paid' => true];
                     //$socket_data->result = $request_result;
         
                     $socketData = ['event' => 'payment_done_'.$get_driverrequest->userDetail->slug,'message' => $socket_data];
                     sendSocketData($socketData);
 
                     $pushData = ['notification_enum' => PushEnum::PAYMENT_CHANGE];
                     dispatch(new SendPushNotification($title, $sub_title,$pushData, $get_driverrequest->userDetail->device_info_hash, $get_driverrequest->userDetail->mobile_application_type,0));



                }

                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }

}
