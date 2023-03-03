<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\boilerplate\Country;
use DB;
use App\Models\User;
use File;
use Validator;

class WalletController extends BaseController
{
    public function walletList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);

            $countrydetails = Country::find($user->country_code);
            if(is_null($countrydetails))
                return $this->sendError('No country details found',[],404);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            $wallet_total = Wallet::where('user_id',$user->id)->first();
            
            $walletListTransaction = WalletTransaction::where('user_id',$user->id)->orderBy('created_at', 'DESC')->get();


            if(is_null($walletListTransaction)){
                return $this->sendError('No Data Found',[],404);
            }
            if(is_null($wallet_total)){
                $data['total_amount'] = 0;
                $data['currency'] = $countrydetails->currency_symbol;
            } 
            else {
            $data['total_amount']       =(int) $wallet_total['balance_amount'];
            $data['currency']           = $countrydetails->currency_symbol;
            $data['wallet_transaction'] = $walletListTransaction;
            
            // foreach($walletListTransaction  as $key=>$value){

            //     $data['wallet_details'][$key]['created_at'] = $value['created_at'];
            //     // $data['wallet_details'][$key]['name'] = $value['purpose'];
            //     $data['wallet_details'][$key]['request_id'] = $value['request_id'];

            //     // $object->$key = $value;
            //     // $data['wallet_details']['wallet_transaction']['name'] = $value['purpose'];
            //     //$data['wallet_details'][$key]['wallet_transaction'] = $walletListTransactions;

            // }
            }
               
            return $this->sendResponse('Data Found',$data,200);  
         
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'amount' => 'required',    
            'purpose' => 'required', 
           
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

                $driver_wallet = Wallet::where('user_id',$user->id)->first();
                if (is_null($driver_wallet)) 
                {
                   $driver_wallet = new Wallet();
                   $driver_wallet->user_id = $user->id;
                   $driver_wallet->earned_amount = $request['amount'];
                   $driver_wallet->balance_amount = $request['amount'];
                   $driver_wallet->save();
                } 
                else 
                {

                $driver_wallet->earned_amount	+= $request['amount'];
                $driver_wallet->balance_amount	+= $request['amount'];
                $driver_wallet->save();
                }

                $walletTransaction = new WalletTransaction();
                $walletTransaction->wallet_id = $driver_wallet->id;
                $walletTransaction->user_id   = $user->id;
                $walletTransaction->amount	  = $request['amount'];
                $walletTransaction->purpose   = $request['purpose']; 
                $walletTransaction->save();

                $response['wallet']  = $driver_wallet;
                $response['wallet_transaction'] = $walletTransaction; 

            

                DB::commit();
                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }


    public function edit(Request $request,$slug)
    {

        $validator = Validator::make($request->all(),[
            'title' => 'required',    
            'address' => 'required',           
            'latitude' => 'required',   
            'longitude' => 'required'      
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


                $favouite = Favourite::where('slug',$slug)->first();
                if(is_null($favouite))
                    return $this->sendError('Unauthorized',[],401);
                    
                $favouite->title = strip_tags(trim($request->input('title')));
                $favouite->address = strip_tags(trim($request->input('address')));
                $favouite->latitude	 = strip_tags(trim($request->input('latitude')));
                $favouite->longitude = strip_tags(trim($request->input('longitude')));

                $favouite->user_id = $user->id;
                $favouite->status = '1';
                $favouite->update();
                $response['favouite'] = $favouite;

                DB::commit();
                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }


    public function destroy($slug){ 


        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);

            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

              
        $favouriteplaces = Favourite::where('slug',$slug)->first();
      
        if(!is_null($favouriteplaces))
        {
            try{
                $favouriteplaces->delete();
                return $this->sendResponse('Data Found',[],200); 
            } catch (\Exception $e) {
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }
        }
            else
            {
            return response()->json(['message' =>'failure.','error'=>'true'], 404);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
        }
              
    }


}
