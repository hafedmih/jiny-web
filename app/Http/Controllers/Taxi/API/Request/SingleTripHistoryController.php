<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Favourite;
use App\Models\taxi\Requests\RequestPlace;
use DB;
use App\Models\User;
use File;
use Validator;
use App\Models\taxi\Requests\RequestBill;



class SingleTripHistoryController extends BaseController
{

    public function SingleTripHistoryList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            // dd($user);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            $validator = Validator::make($request->all(),
            [
                'request_id' => 'required',       
            ]);
            if($validator->fails()){
                return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
            }

            $req = RequestModel::where('driver_id','=',$user->id)->where('id',$request['request_id'])->first();

            if($req!='')
            {

                $req = RequestModel::where('driver_id','=',$user->id)->where('id',$request['request_id'])->first();


            }
            else {
                return $this->sendError('No Data Found',[],404); 
            }
            
            
            $request_bill = RequestBill::where('request_id',$req->id)->orderBy('created_at', 'DESC')->first();

            $request_result =  fractal($req, new TripRequestTransformer)->parseIncludes('requestBill');

            return $this->sendResponse('Data Found',$request_result,200); 


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

}