<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\PassengerUploadImages;
use App\Models\taxi\UserInstantTrip;
use DB;
use App\Models\User;
use File;
use Validator;


class InstantImageUploadController extends BaseController
{

    public function instantimageupload(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'request_id' => 'required', 
            // 'user_instant_image' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            // 'driver_instant_image' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
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

               $instantupload        =  PassengerUploadImages::where('user_id',$user->id)->get();
               $instant_user_get     =  UserInstantTrip::where('request_id','=',$request['request_id'])->first(); 
               $requests             =  RequestModel::where('id',$request->request_id)->first();
               $null = '';
               if($request->has('user_instant_image'))
                {
               $user_instant_image   =  uploadImage('images/passengers',$request->file('user_instant_image'),$null);
                }
                if($request->has('driver_instant_image'))
                {
               $driver_instant_image =  uploadImage('images/passengers',$request->file('driver_instant_image'),$null);
                }

               if($instantupload)
               {
                    if (PassengerUploadImages::where('user_id',$user->id)->where('request_id','=',$request['request_id'])->exists()) {
                        return $this->sendError('Image already exists',[],403);
                    }
                    else {

                if($request->has('user_instant_image'))
                {
                    $instantupload = new PassengerUploadImages();
                    $instantupload->request_id = $request['request_id'];
                    $instantupload->user_id = null;
                    $instantupload->driver_id = $user->id;
                    $instantupload->image = $user_instant_image;
                    $instantupload->user_upload_image = 1;
                    $instantupload->upload = 'USER';
                    $instantupload->upload_time = NOW();
                    $instantupload->status = 1;
                    $instantupload->save();
                }

                if($request->has('driver_instant_image'))
                {
                    $instantupload = new PassengerUploadImages();
                    $instantupload->request_id = $request['request_id'];
                    $instantupload->user_id = null;
                    $instantupload->driver_id = $user->id;
                    $instantupload->image = $driver_instant_image;
                    $instantupload->driver_upload_image = 1;
                    $instantupload->upload = 'DRIVER';
                    $instantupload->upload_time = NOW();
                    $instantupload->status = 1;
                    $instantupload->save();
                }

                    $upload_image_driver = PassengerUploadImages::where('request_id',$request['request_id'])->where('upload','DRIVER')->where('driver_upload_image',1)->first();

                    if(!is_null($upload_image_driver)){
                        $instantupload->driver_upload_image = true;
                    }else{
                        $instantupload->driver_upload_image = false;
                    }
                    $upload_image_user = PassengerUploadImages::where('request_id',$request['request_id'])->where('upload','USER')->where('user_upload_image',1)->first();

                    if(!is_null($upload_image_user)){
                        $instantupload->user_upload_image = true;
                    }else{
                        $instantupload->user_upload_image = false;
                    }

                    }
                }
                $response['instant_image_upload'] = $instantupload;

                DB::commit();
                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }


   
}
