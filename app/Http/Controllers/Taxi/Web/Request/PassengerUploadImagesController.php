<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\taxi\PassengerUploadImages;
use App\Models\taxi\Requests\Request as RequestModel;

use Validator;
use Carbon\Carbon;
use DB;

class PassengerUploadImagesController extends BaseController
{
    public function passengerUploadImages(Request $request)
    {
        try{

            DB::beginTransaction(); 

            $validator = Validator::make($request->all(), [
                'images' => 'required',
                'request_id' => 'required'
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);       
            }

            $clientlogin = $this::getCurrentClient(request());
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            $requests = RequestModel::where('id',$request->request_id)->first();

            $null = '';

            $filename =  uploadImage('images/passengers',$request->file('images'),$null);

            $PassengerUploadImages = PassengerUploadImages::create([
                'request_id' => $requests->id,
                'driver_id' => $requests->driver_id,
                'user_id' => $requests->user_id,
                'image' => $filename,
                'upload_time' => NOW(),
                'status' => 1
            ]);

            if($user->hasRole('driver')){
                $PassengerUploadImages->upload = 'DRIVER';
            }
            else{
                $PassengerUploadImages->upload = 'USER';
            }

            $PassengerUploadImages->save();

            DB::commit();
            return $this->sendResponse('Image Upload Successfully',$PassengerUploadImages,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
