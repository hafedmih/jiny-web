<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Vehicle;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Zone;
use DB;
use File;
use Validator;
use App\Models\User;

use App\Traits\CommanFunctions;

class ServiceLocationController extends BaseController
{

    use CommanFunctions;

    
    public function ServiceLocationList(Request $request)
    {
        try{ 
            $Servicelocation = Zone::select('slug','zone_name')->where('status', 1)->where('zone_level','PRIMARY')->get();
            if(is_null($Servicelocation)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data['ServiceLocation'] = $Servicelocation;
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function checkzone(Request $request)
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

            $data = $request->all();

            // get zone use pickup lat and long
            $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);
            if($zone){
                if($zone->non_service_zone == 'No'){
                    $data['zone'] = true;
                }
                else{
                    $data['zone'] = false;
                }
            }else {
                $data['zone'] = false;

            }
            // // dd($zone);
            // if(is_null($zone))
            //     return $this->sendError('Non services zone',[],404);

            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    
    }

    public function checkoutstation(Request $request)
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

            $data = $request->all();

            // get zone use pickup lat and long
            $zone = $this->getZone($data['pickup_lat'], $data['pickup_long']);

            if($zone == null)
            {
                $data['outstation'] = true;
            }else {
                $data['outstation'] = false;
            }
            
            
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    
    }
}
