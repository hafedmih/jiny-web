<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\taxi\Notification;
use App\Models\User;

use DB;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class NotificationController extends BaseController
{
    public function notificationList(Request $request)
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
            
            $notification = Notification::where('status',1)->orderBy('date','desc')->get();
            foreach ($notification as $key => $value) {
                if($user->hasRole('driver')){
                    if(!in_array($user->id, explode(",",$value->driver_id))){
                        unset($notification[$key]);
                    }
                }
                if($user->hasRole('user')){
                    if(!in_array($user->id, explode(",",$value->user_id))){
                        unset($notification[$key]);
                    }
                }
            }
            
            if(is_null($notification)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $general = [];
                $trip = [];
                foreach ($notification as $key => $value) {
                    if($value->notification_type == "GENERAL"){
                        array_push($general, $value);
                    }
                    if($value->notification_type == "TRIP"){
                        array_push($trip, $value);
                    }
                }
                $data['general'] = $general;
                $data['trip'] = $trip;

                $data = $this->paginate($general);

                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);
        return new LengthAwarePaginator($itemstoshow ,$total   ,$perPage);
    }
}
