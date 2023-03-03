<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Constants\Promo;

use App\Models\boilerplate\Country;
use App\Models\User;
use App\Models\taxi\Vehicle;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Driver;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestBill;
use App\Transformers\Request\TripRequestTransformer;

use App\Http\Requests\Taxi\API\Request\SingleTripHistoryRequest;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



use File;
use Validator;
use DB;


class TripHistoryController extends BaseController
{


    public function TripHistory(Request $request)
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

            $data = [];
            
            $validator = Validator::make($request->all(),[
                'ride_type' => 'required',    
                'trip_type' => 'required',                 
            ]);
            if($validator->fails()){
                return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
            }

            $req = '';
            $data = [];
            $request_result =[];
            // dd($user);
            if($user->hasRole('user')){
                if($request->ride_type == 'RIDE NOW'){
                    if($request->trip_type == 'COMPLETED')
                    {
                        $req = RequestModel::where('is_completed',1)->where('is_cancelled',0)->where('user_id',$user->id)->orderBy('created_at', 'DESC')->get();

                        // $req = RequestModel::where('is_completed',1)->where('is_later',0)->get();
                       
                        
                    }elseif($request->trip_type == 'CANCELLED')
                    {
                        $req = RequestModel::where('is_cancelled',1)->where('user_id',$user->id)->where('driver_id','!=',null)->where('is_driver_started',1)->orderBy('created_at', 'DESC')->where('is_later',0)->get();
                       

                    }elseif($request->trip_type == 'ALL')
                    {
                        $req = RequestModel::where('is_cancelled',1)->where('user_id',$user->id)->orderBy('created_at', 'DESC')->get();
                       

                    }
                }elseif($request->ride_type == 'RIDE LATER'){
                    if($request->trip_type == 'COMPLETED')
                    {
                        $req = RequestModel::where('is_completed',1)->where('is_cancelled',0)->where('user_id',$user->id)->orderBy('created_at', 'DESC')->where('is_later',1)->get();
                       

                       // return $req->links();
                    }elseif($request->trip_type == 'CANCELLED')
                    {
                        $req = RequestModel::where('is_cancelled',1)->where('user_id',$user->id)->orderBy('created_at', 'DESC')->where('is_later',1)->get();
                     

                       // return $req->links();
                    }elseif($request->trip_type == 'ALL')
                    {
                        $req = RequestModel::where('user_id',$user->id)->where('is_cancelled','!=',1)->where('is_completed','!=',1)->where('is_later',1)->orderBy('created_at', 'DESC')->get();

                     //   return $req->links();
                    }
                    elseif($request->trip_type == 'ALL')
                    {
                        $req = RequestModel::where('user_id',$user->id)->where('is_later',1)->get();

                      //  return $req->links();
                    }
                }else{
                    // Ride Later Block
                    return $this->sendError('Wrong request type',[],404);
                }
            }else{
                if($request->ride_type == 'RIDE NOW'){
                    if($request->trip_type == 'COMPLETED')
                    {
                        $req = RequestModel::where('is_completed',1)->where('is_cancelled',0)->where('driver_id',$user->id)->orderBy('created_at', 'DESC')->get();
                        
                    }elseif($request->trip_type == 'CANCELLED')
                    {
                        $req = RequestModel::where('is_cancelled',1)->where('driver_id',$user->id)->orderBy('created_at', 'DESC')->get();
                 
                    }
                }else{
                    // Ride Later Block
                    return $this->sendError('Wrong request type',[],404);
                } 
            }
            if(is_null($req)){
                return $this->sendError('No Record Found',[],404);
            }else{

                foreach ($req as $key => $requestlist)
                {
                 
                    if($requestlist->is_cancelled == 1 && $requestlist->driver_id != null && $requestlist->is_driver_started  != 0){
                        $request_result[$key] =  fractal($requestlist, new TripRequestTransformer);
                    }else{

                        $request_bill = RequestBill::where('request_id',$requestlist->id)->orderBy('created_at', 'DESC')->first();
                        
                        if(is_null($request_bill)){
                            $request_result[$key] =  fractal($requestlist, new TripRequestTransformer);    
                        }else{

                            
                            $request_result[$key] =  fractal($requestlist, new TripRequestTransformer)->parseIncludes('requestBill');
                        }
                        
                     
                    }   
                    
                }

                $request_result = $this->paginate($request_result);

                return $this->sendResponse('Data Found',$request_result,200);  
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
