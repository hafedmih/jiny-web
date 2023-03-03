<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\GoHome;
use DB;
use App\Models\User;
use Validator;


class GoHomeController extends BaseController
{

    public function GoHomeList(Request $request)
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

            $gohome_list = GoHome::where('user_id',$user->id)->groupBy('address')->orderBy('id', 'DESC')->get();
           
                $data['GoHomeList'] = $gohome_list;
                return $this->sendResponse('Data Found',$data,200);  

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function store(Request $request)
    {

        $key_id = "rzp_live_gXSsYt01xINj1M";
        $secret = "3MshNo2WgXG3csB3ZwyTDbb4";

        $api = new Api($key_id, $secret);

       $$api->order->create(array('receipt' => '123', 'amount' => 100, 'currency' => 'INR', 'notes'=> array('key1'=> 'value3','key2'=> 'value2')));

        // $validator = Validator::make($request->all(),[
        //     'address' => 'required',           
        //     'lat'     => 'required',               
        //     'lng'     => 'required',  
           
        // ]);
        // if($validator->fails()){
        //     return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
        // }

        // try{
        //     $clientlogin = $this::getCurrentClient(request());
      
        //     if(is_null($clientlogin)) 
        //         return $this->sendError('Token Expired',[],401);
         
        //     $user = User::find($clientlogin->user_id);

        //     if(is_null($user))
        //         return $this->sendError('Unauthorized',[],401);
            
        //     if($user->active == false)
        //         return $this->sendError('User is blocked so please contact admin',[],403);
            
        //        $gohome_query =  GoHome::where('user_id',$test)->get();
        //        if($gohome_query)
        //        {
        //             if (GoHome::where('user_id',$user->id)->where('address','=',$request['address'])->exists()) {
        //                 return $this->sendError('Address already exists',[],403);
        //             }
        //             else {

        //             $gohome_query = new GoHome();
        //             $gohome_query->address = $request['address'];
        //             $gohome_query->lat	   = $request['lat'];
        //             $gohome_query->lng     = $request['lng'];
        //             $gohome_query->user_id = $user->id;
        //             $gohome_query->status  = 1;
        //             $gohome_query->save();
        //             }
        //         }
        //         $response['gohome'] = $gohome_query;

        //         DB::commit();
        //         return $this->sendResponse('Data Found',$response,200); 


        //     } catch (\Exception $e) {
        //         DB::rollback();
        //         return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
        //     }

    }


    public function GohomeEnable(Request $request)
    {
      
        $validator = Validator::make($request->all(),[
            'gohome_enable' => 'required',
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

            $users_query = User::where('slug',$user->slug)->first();

            if(is_null($users_query))
                return $this->sendError('Unauthorized',[],401);
                
            $users_query->gohome_enable = $request->gohome_enable;
            $users_query->update();
            $response['gohome'] = $users_query;

            DB::commit();
            return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }


    }


}
