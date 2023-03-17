<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\RiderAddress;
use App\Models\User;
use DB;

class RiderAddressController extends BaseController
{
        public function rideraddress(Request $request)
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
    
                    if($request->has('search'))
                    {
                       $ride_list = RiderAddress::select('title','latitude','longitude')->where('title','LIKE', "%{$request->search}%")->limit(5)->get();
                    }
                    
                if(is_null($ride_list)){
                    return $this->sendError('No Data Found',[],404);  
                }
                    return $this->sendResponse('Data Found',$ride_list,200);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Catch error','failure.'.$e,400);  
            }
        }
    
}
