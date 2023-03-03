<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Order;
use App\Models\taxi\Requests\RequestPlace;
use DB;
use App\Models\User;
use File;
use Validator;
use Razorpay\Api\Api;



class PaymentController extends BaseController
{

    public function OrderGenerate(Request $request)
    {
       
        $validator = Validator::make($request->all(),[
            'amount' => 'required',    
            'currency' => 'required',             
           
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

                $key_id = env('RAZORPAY_KEY');
                $secret = env('RAZORPAY_SECRET');

                $api = new Api($key_id, $secret);

                $order_generate  = $api->order->create(array('receipt' => $request->receipt, 'amount' => $request->amount, 'currency' => $request->currency));
                
                $orders = new Order();
                $orders->user_id  = $user->id;
                $orders->order_id = $order_generate->id;
                $orders->currency = $request->currency;
                $orders->amount   = $request->amount;
                $orders->receipt  = $request->receipt;
                $orders->status   = $order_generate->status;
                $orders->key_id   = $key_id;
                $orders->save();

                $response['orders'] = $orders;

                DB::commit();
                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }

}
