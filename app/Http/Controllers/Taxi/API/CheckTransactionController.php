<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;
use File;
use Validator;
use App\Jobs\SendPushNotification;
use App\Constants\PushEnum;
use App\Traits\CommanFunctions;
use App\Models\User;
use App\Models\taxi\Transaction;


class CheckTransactionController extends BaseController
{
    use CommanFunctions;

    public function checkTransaction(Request $request)
    {
        try {

            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

                $validator = Validator::make($request->all(),[
                    'operation_id' => 'required',
                    'request_id' => 'required',
                    'amount' => 'required',
                    'payment_status' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
                }

               $request_check = RequestModel::where('id',$request->request_id)->where('is_paid',0)->where('is_cancelled',0)->first();

               if(is_null($request_check)){
                 return $this->sendError('No Request Found',[],404);  
               }

               if($request_check->driver_id){

               if($request->payment_status == "TS")
               {
                    $transaction = new Transaction();
                    $transaction->request_id = $request['request_id'];
                    $transaction->user_id = $request_check->user_id;
                    $transaction->transaction_id = $request['transaction_id'];
                    $transaction->amount = $request['amount'];
                    $transaction->payment_status = $request['payment_status'];
                    $transaction->operation_id = $request['operation_id'];
                    $transaction->is_paid = 1;
                    $transaction->save();

                    $request_check->is_paid = 1;
                    $request_check->update();

                    $this->walletTransaction($request['amount'],$request_check->driver_id,'EARNED','Trip Amount',$request->request_id);



                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'user-payment-done');
                    if(is_null($push_data)){
                        $title     = "User payment completed";
                        $body      = "User payment completed";
                        $sub_title = "User payment completed";

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
        
                    $socketData = ['event' => 'payment_status_'.$user->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::USER_PAYMENT_DONE];
                    dispatch(new SendPushNotification($title,$pushData, $user->device_info_hash, $user->mobile_application_type,0,$sub_title));


                    // @ TODO Driver push and socket

                    $request_push_driver = RequestModel::where('id',$request->request_id)->with('driverDetail')->first();

                     // Form a socket sturcture using users'id and message with event name
                     $socket_data = new \stdClass();
                     $socket_data->success = true;
                     $socket_data->success_message  = PushEnum::USER_PAYMENT_DONE;
                     $socket_data->result = ['is_paid' => true];
                     //$socket_data->result = $request_result;
         
                     $socketData = ['event' => 'payment_status_'.$request_push_driver->driverDetail->slug,'message' => $socket_data];
                     sendSocketData($socketData);
 
                     $pushData = ['notification_enum' => PushEnum::USER_PAYMENT_DONE];
                     dispatch(new SendPushNotification($title,$pushData, $request_push_driver->driverDetail->device_info_hash, $request_push_driver->driverDetail->mobile_application_type,0,$sub_title));

                     return $this->sendResponse('Payment Completed',[],200); 
                }

               }elseif($request->payment_status == "TF")
               {

                    if ($user) {
                        $title = Null;
                        $body = '';
                        $lang = $user->language;
                        $push_data = $this->pushlanguage($lang,'user-payment-done');
                        if(is_null($push_data)){
                            $title     = "User payment has been failed please try again later";
                            $body      = "User payment has been failed please try again later";
                            $sub_title = "User payment has been failed please try again later";

                        }else{
                            $title     =  $push_data->title;
                            $body      =  $push_data->description;
                            $sub_title =  $push_data->description;

                        }   
                        
                    // @ TODO User get push and socket

                        // Form a socket sturcture using users'id and message with event name
                        $socket_data = new \stdClass();
                        $socket_data->success = true;
                        $socket_data->success_message  = PushEnum::PAYMENT_FAIL;
                        $socket_data->result = ['is_paid' => false];
            
                        $socketData = ['event' => 'payment_status_'.$user->slug,'message' => $socket_data];
                        sendSocketData($socketData);

                        $pushData = ['notification_enum' => PushEnum::PAYMENT_FAIL];
                        dispatch(new SendPushNotification($title,$pushData, $user->device_info_hash, $user->mobile_application_type,0,$sub_title));


                        // @ TODO Driver push and socket

                        $request_fail_push = RequestModel::where('id',$request->request_id)->with('driverDetail')->first();

                        // Form a socket sturcture using users'id and message with event name
                        $socket_data = new \stdClass();
                        $socket_data->success = true;
                        $socket_data->success_message  = PushEnum::PAYMENT_FAIL;
                        $socket_data->result = ['is_paid' => false];
                        //$socket_data->result = $request_result;
            
                        $socketData = ['event' => 'payment_status_'.$request_fail_push->driverDetail->slug,'message' => $socket_data];
                        sendSocketData($socketData);

                        $pushData = ['notification_enum' => PushEnum::PAYMENT_FAIL];
                        dispatch(new SendPushNotification($title,$pushData, $request_fail_push->driverDetail->device_info_hash, $request_fail_push->driverDetail->mobile_application_type,0,$sub_title));

                        return $this->sendError('Your payment has been failed please try again later', [], 400);
                    }
               }
               
               else {

                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'user-payment-done');
                    if(is_null($push_data)){
                        $title     = "User payment has been suspended please try again later";
                        $body      = "User payment has been suspended please try again later";
                        $sub_title = "User payment has been suspended please try again later";

                    }else{
                        $title     =  $push_data->title;
                        $body      =  $push_data->description;
                        $sub_title =  $push_data->description;

                    }   
                    
                // @ TODO User get push and socket

                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = false;
                    $socket_data->success_message  = PushEnum::PAYMENT_FAIL;
                    $socket_data->result = ['is_paid' => false];
        
                    $socketData = ['event' => 'payment_status_'.$user->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::PAYMENT_FAIL];
                    dispatch(new SendPushNotification($title,$pushData, $user->device_info_hash, $user->mobile_application_type,0,$sub_title));


                    // @ TODO Driver push and socket

                    $driver_push_failed = RequestModel::where('id',$request['request_id'])->with('driverDetail')->first();

                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::PAYMENT_FAIL;
                    $socket_data->result = ['is_paid' => false];
                    //$socket_data->result = $request_result;
        
                    $socketData = ['event' => 'payment_status_'.$driver_push_failed->driverDetail->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::PAYMENT_FAIL];
                    dispatch(new SendPushNotification($title,$pushData, $driver_push_failed->driverDetail->device_info_hash, $driver_push_failed->driverDetail->mobile_application_type,0,$sub_title));

                    return $this->sendError('Your payment has been suspended please try again later', [], 400);
                }


               }
            }
           
            
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
        }
    }
}
