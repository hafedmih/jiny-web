<?php

namespace App\Http\Controllers\Taxi\API;

use App\Constants\PushEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Requests\RequestPlace;
use DB;
use App\Models\User;
use File;
use Validator;
use Razorpay\Api\Api;
use App\Traits\CommanFunctions;
use App\Jobs\SendPushNotification;



class ChangePaymentController extends BaseController
{
    use CommanFunctions;


    public function ChangePayment(Request $request)
    {
       
        $validator = Validator::make($request->all(),[
            'request_id' =>  'required',    
            'payment_opt' => 'required', // CASH,CARD,WALLET
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

             $paymentOpt = $this->validatePaymentOption($request);


               $getrequest = RequestModel::where('id',$request['request_id'])->where('is_paid',0)->first();

               if(is_null($getrequest)){
                 return $this->sendError('No Request Found',[],404);  
               }
               $getrequest->payment_opt = $request['payment_opt'];
               //$getrequest->is_paid = 1;
               $getrequest->update();

               $response['changepayment'] = $getrequest;

                DB::commit();

                if ($user) {
                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'user-change-payment');
                    if(is_null($push_data)){
                        $title     = "Your payment mode changed";
                        $body      = "Your payment mode changed";
                        $sub_title = "Your payment mode changed";

                    }else{
                        $title     =  $push_data->title;
                        $body      =  $push_data->description;
                        $sub_title =  $push_data->description;

                    }   
                    
                   // @ TODO User get push and socket

                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::USER_PAYMENT_CHANGE;
                    //$socket_data->result = $request_result;
                    
        
                    $socketData = ['event' => 'payment_changed_'.$user->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::USER_PAYMENT_CHANGE];
                    dispatch(new SendPushNotification($title, $sub_title,$pushData, $user->device_info_hash, $user->mobile_application_type,0));


                    $title = Null;
                    $body = '';
                    $lang = $user->language;
                    $push_data = $this->pushlanguage($lang,'user-change-payment');
                    if(is_null($push_data)){
                        $title     = "Your payment mode changed";
                        $body      = "Your payment mode changed";
                        $sub_title = "Your payment mode changed";

                    }else{
                        $title     =  $push_data->title;
                        $body      =  $push_data->description;
                        $sub_title =  $push_data->description;

                    }  

                    // @ TODO Driver push and socket

                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message  = PushEnum::USER_PAYMENT_CHANGE;
                    //$socket_data->result = $request_result;
                    

                    $get_driverrequest = RequestModel::where('id',$request['request_id'])->where('is_paid',0)->with('userDetail')->first();
                    
                  //  dd($get_driverrequest);

        
                    $socketData = ['event' => 'payment_changed_'.$get_driverrequest->driverDetail->slug,'message' => $socket_data];
                    sendSocketData($socketData);

                    $pushData = ['notification_enum' => PushEnum::USER_PAYMENT_CHANGE];
                    dispatch(new SendPushNotification($title, $sub_title,$pushData, $get_driverrequest->driverDetail->device_info_hash, $get_driverrequest->userDetail->mobile_application_type,0));
                }


                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }

    public function validatePaymentOption($request)
    {
        switch ($request->payment_opt) {
            case "CARD": // Card payment
                return true;
                break;
            case "CASH": // Cash payment
                return true;
                break;
            case "WALLET": // Wallet payment
                return true;
                break;
        }
    }

}