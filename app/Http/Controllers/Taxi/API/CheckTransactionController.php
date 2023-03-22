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



class CheckTransactionController extends BaseController
{
    use CommanFunctions;
    public function checkTransaction(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'operationID' => 'required',
            'token' => 'required',
            'refresh_token' => 'required',
            'grant_type' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
        }
        try {

            $data = [
                'operationId' => $request->operationID,
            ];
            $refresh_token_data = [
                'refresh_token' => $request->refresh_token,
                'grant_type' => $request->grant_type,
                'password' => $request->password,
            ];

            $token = $request->token;

            $get_data = $this->transactionCheck($token, $data);

            $response = json_decode($get_data);
                
// dd($response);
                if(isset($response->errorCode) && $response->errorCode == 0 && $response->status =! null){
                    if($response->status == 'TF'){
                        
                                $title =
                                    'Payment failed please try again or change payment method';
                                $body =
                                    'Payment failed please try again or change payment method';
                                $sub_title =
                                    'Payment failed please try again or change payment method';
                           
    
                            // @ TODO User get push and socket
                            // Form a socket sturcture using users'id and message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message =
                                PushEnum::PAYMENT_FAIL;
                            
                            $socketData = [
                                'event' =>
                                    'payment_failed_' .
                                    $response->transactionId,
                                'message' => $socket_data,
                            ];
                            sendSocketData($socketData);
                            $pushData = [
                                'notification_enum' => PushEnum::PAYMENT_FAIL,
                            ];
                            dispatch(
                                new SendPushNotification(
                                    $title,
                                    $sub_title,
                                    $pushData,
                                    $response->transactionId,
                                    0
                                )
                            );
                    }elseif($response->status == 'TA'){

                        $title =
                        'Transaction pending';
                            $body =
                                'Transaction pending';
                            $sub_title =
                                'Transaction pending';
                    

                        // @ TODO User get push and socket
                        // Form a socket sturcture using users'id and message with event name
                        $socket_data = new \stdClass();
                        $socket_data->success = true;
                        $socket_data->success_message =
                            PushEnum::PAYMENT_PENDING;
                        
                        $socketData = [
                            'event' =>
                                'payment_pending_' .
                                $response->transactionId,
                            'message' => $socket_data,
                        ];
                        sendSocketData($socketData);
                        $pushData = [
                            'notification_enum' => PushEnum::PAYMENT_PENDING,
                        ];
                        dispatch(
                            new SendPushNotification(
                                $title,
                                $sub_title,
                                $pushData,
                                $response->transactionId,
                                0
                            )
                        );
                    }elseif($response->status == 'TS'){
                        
                        $title = 'Transaction completed successfully';
                        $body = 'Transaction completed successfully';
                        $sub_title = 'Transaction completed successfully';
                            

                            // @ TODO User get push and socket
                            // Form a socket sturcture using message with event name
                            $socket_data = new \stdClass();
                            $socket_data->success = true;
                            $socket_data->success_message =
                                PushEnum::PAYMENT_DONE;
                            
                            $socketData = [
                                'event' =>
                                    'payment_done_' .
                                    $response->transactionId,
                                'message' => $socket_data,
                            ];
                            sendSocketData($socketData);
                            $pushData = [
                                'notification_enum' =>
                                    PushEnum::PAYMENT_DONE,
                            ];
                            dispatch(
                                new SendPushNotification(
                                    $title,
                                    $sub_title,
                                    $pushData,
                                    $response->transactionId,
                                    0
                                )
                            );
                        
                        // return $this->sendResponse('Data Found',$response,200);
                    }
                }else{

                    if(isset($response->errorMessage) && $response->errorMessage == 'EXCEPTION WHEN CALLING : 401 null'){

                        // @ TODO Refresh token Api call
                        $get_response = $this->refreshToken($refresh_token_data);
                        $refreshToken = json_decode($get_response);
                        if($refreshToken->error){
                            return response()->json($refreshToken); //
                        }
                          

                        $token_data = $refreshToken->access_token;
                        if(isset($token_data)){
                            $get_data = $this->transactionCheck($token_data, $data);
                            $response = json_decode($get_data);
                                
                                if(isset($response->errorCode) && $response->errorCode == 0 && $response->status =! null){
                                    if($response->status == 'TF'){
                                        
                                                $title =
                                                    'Payment failed please try again or change payment method';
                                                $body =
                                                    'Payment failed please try again or change payment method';
                                                $sub_title =
                                                    'Payment failed please try again or change payment method';
                                           
                    
                                            // @ TODO User get push and socket
                                            // Form a socket sturcture using users'id and message with event name
                                            $socket_data = new \stdClass();
                                            $socket_data->success = true;
                                            $socket_data->success_message =
                                                PushEnum::PAYMENT_FAIL;
                                            
                                            $socketData = [
                                                'event' =>
                                                    'payment_failed_' .
                                                    $response->transactionId,
                                                'message' => $socket_data,
                                            ];
                                            sendSocketData($socketData);
                                            $pushData = [
                                                'notification_enum' => PushEnum::PAYMENT_FAIL,
                                            ];
                                            dispatch(
                                                new SendPushNotification(
                                                    $title,
                                                    $sub_title,
                                                    $pushData,
                                                    $response->transactionId,
                                                    0
                                                )
                                            );
                                    }elseif($response->status == 'TA'){
                
                                        $title =
                                        'Transaction pending';
                                            $body =
                                                'Transaction pending';
                                            $sub_title =
                                                'Transaction pending';
                                    
                
                                        // @ TODO User get push and socket
                                        // Form a socket sturcture using users'id and message with event name
                                        $socket_data = new \stdClass();
                                        $socket_data->success = true;
                                        $socket_data->success_message =
                                            PushEnum::PAYMENT_FAIL;
                                        
                                        $socketData = [
                                            'event' =>
                                                'payment_failed_' .
                                                $response->transactionId,
                                            'message' => $socket_data,
                                        ];
                                        sendSocketData($socketData);
                                        $pushData = [
                                            'notification_enum' => PushEnum::PAYMENT_FAIL,
                                        ];
                                        dispatch(
                                            new SendPushNotification(
                                                $title,
                                                $sub_title,
                                                $pushData,
                                                $response->transactionId,
                                                0
                                            )
                                        );
                                    }elseif($response->status == 'TS'){
                                        
                                        $title = 'Transaction completed successfully';
                                        $body = 'Transaction completed successfully';
                                        $sub_title = 'Transaction completed successfully';
                                            
                
                                            // @ TODO User get push and socket
                                            // Form a socket sturcture using message with event name
                                            $socket_data = new \stdClass();
                                            $socket_data->success = true;
                                            $socket_data->success_message =
                                                PushEnum::PAYMENT_DONE;
                                            
                                            $socketData = [
                                                'event' =>
                                                    'payment_done_' .
                                                    $response->transactionId,
                                                'message' => $socket_data,
                                            ];
                                            sendSocketData($socketData);
                                            $pushData = [
                                                'notification_enum' =>
                                                    PushEnum::PAYMENT_DONE,
                                            ];
                                            dispatch(
                                                new SendPushNotification(
                                                    $title,
                                                    $sub_title,
                                                    $pushData,
                                                    $response->transactionId,
                                                    0
                                                )
                                            );
                                        
                                        // return $this->sendResponse('Data Found',$response,200);
                                    }
                                }else{
                                    // return response()->json($response);
                                    return response()->json(['message' =>$response->errorMessage,'error'=>'true'], 400); 
                                }
                        }
                    }
                    // return response()->json($response);
                    return response()->json(['message' =>$response->errorMessage,'error'=>'true'], 400); 
                }
               

                // return response()->json(json_decode($response));
           
            
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
        }
    }
}
