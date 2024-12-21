<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Favourite;
use App\Models\taxi\Requests\RequestPlace;
use DB;
use App\Models\User;
use File;
use Validator;


class FavouriteController extends BaseController
{

    public function FavouriteList(Request $request)
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
                
            // $requestList = RequestModel::where('user_id',$user->id)->get();
           
            // foreach ($requestList as $key => $value)
            // {
            //     if($value['trip_type']== "LOCAL")
            //     {
            //         $requestList = RequestModel::join('request_places', 'requests.id', '=', 'request_places.request_id')->latest()->groupBy('request_places.drop_address')->where('requests.user_id',$user->id)->where('requests.trip_type',"LOCAL")->whereNotNull('request_places.drop_address')->limit(2)->get(['request_places.pick_lat','request_places.pick_lng','request_places.drop_lat','request_places.drop_lng','request_places.pick_address','request_places.drop_address','request_places.created_at','request_places.updated_at']);
            //     }
              
            // }
            $requestList = RequestModel::join('request_places', 'requests.id', '=', 'request_places.request_id')->latest()->groupBy('request_places.drop_address')->where('requests.user_id',$user->id)->where('requests.trip_type',"LOCAL")->whereNotNull('request_places.drop_address')->limit(2)->get(['request_places.pick_lat','request_places.pick_lng','request_places.drop_lat','request_places.drop_lng','request_places.pick_address','request_places.drop_address','request_places.created_at','request_places.updated_at']);
            $fav_list = Favourite::where('user_id',$user->id)->groupBy('address')->orderBy('id', 'DESC')->get();
           
                $data['FavouriteList'] = $fav_list;
                $data['Last_trip_history'] = $requestList;
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
            'title' => 'required',    
            'address' => 'required',           
            'latitude' => 'required',               
            'longitude' => 'required',  
           
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

               $favouite =  Favourite::where('user_id',$user->id)->get();
               if($favouite)
               {
                    if (Favourite::where('user_id',$user->id)->where('address','=',$request['address'])->exists()) {
                        return $this->sendError('Address already exists',[],403);
                    }
                    else {

                    $favouite = new Favourite();
                    $favouite->title = $request['title'];
                    $favouite->address = $request['address'];
                    $favouite->latitude	 = $request['latitude'];
                    $favouite->longitude = $request['longitude'];
                    $favouite->user_id = $user->id;
                    $favouite->status = '1';
                    $favouite->save();
                    }
                }
                $response['favouite'] = $favouite;

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
