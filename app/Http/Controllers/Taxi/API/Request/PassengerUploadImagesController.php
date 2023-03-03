<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\taxi\PassengerUploadImages;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Jobs\SendPushNotification;
use Validator;
use Carbon\Carbon;
use DB;
use App\Constants\PushEnum;
use App\Models\taxi\UserInstantTrip;



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
            $user_check = User::where('id',$requests->user_id)->first();

             if($user_check) {
                $passengerUploadImages = PassengerUploadImages::create([
                    'request_id' => $requests->id,
                    'driver_id' => $requests->driver_id,
                    'user_id' => $requests->user_id,
                    'image' => $filename,
                    'upload_time' => NOW(),
                    'status' => 1
                ]);
            } else {
                $passengerUploadImages = PassengerUploadImages::create([
                    'request_id' => $requests->id,
                    'driver_id' => $requests->driver_id,
                    'user_id' => null,
                    'image' => $filename,
                    'upload_time' => NOW(),
                    'status' => 1
                ]);
            }

            $check_user = 0;
            $upload_status = false;
            $slug='';
            $check_driver = 0;
            $info_hash = 0;
            $mobile_application_type ='';
            if($user->hasRole('driver')){
                $passengerUploadImages->upload = 'DRIVER';
                $passengerUploadImages->driver_upload_image = 1;
                $passengerUploadImages->save();
                $dd = PassengerUploadImages::where('request_id',$requests->id)->where('upload','DRIVER')->first();
                if(!is_null($dd)){
                    $upload_status = true;
                }
                $check_user = 1;
                $user1 = User::where('id',$requests->user_id)->first();
                if(!is_null($user1)){
                    $slug = $user1->slug;
                    $info_hash = $user1->device_info_hash;
                    $mobile_application_type = $user1->mobile_application_type;
                }
            }
            else{
                $passengerUploadImages->upload = 'USER';
                $passengerUploadImages->user_upload_image = 1;
                $passengerUploadImages->save();
                $dd = PassengerUploadImages::where('request_id',$requests->id)->where('upload','USER')->first();
                if(!is_null($dd)){
                    $upload_status = true;
                }
                $check_driver = 1;
                $user1 = User::where('id',$requests->driver_id)->first();
                if(!is_null($user1)){
                    $slug = $user1->slug;
                    $info_hash = $user1->device_info_hash;
                    $mobile_application_type = $user1->mobile_application_type;
                }
            }


                    $title = Null;
                    $body = '';
                    $title = 'Photo Upload Successfully';
                    $body = 'Photo Upload Successfully';
                    $sub_title = 'Photo Upload Successfully';

             
                $pushData = ['upload_status' => $upload_status,'notification_enum' => PushEnum::UPLOAD_IMAGE_SUCCESS];

                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = "";
                $socket_data->result = ['upload_status' => $upload_status];

                if($upload_status){
                    $pushData['upload_image_url'] = $passengerUploadImages->images1;
                    $socket_data->result['upload_image_url'] = $passengerUploadImages->images1;
                }

                $socketData = ['event' => 'photo_upload_'.$slug,'message' => $socket_data];
                sendSocketData($socketData);

                dispatch(new SendPushNotification($title,$sub_title, $pushData, $info_hash, $mobile_application_type,0));


                $dd1 = PassengerUploadImages::where('request_id',$requests->id)->where('upload','DRIVER')->first();
                if(!is_null($dd1)){
                    $passengerUploadImages->driver_upload_image = true;
                }else{
                    $passengerUploadImages->driver_upload_image = false;
                }
                $dd12 = PassengerUploadImages::where('request_id',$requests->id)->where('upload','USER')->first();
                if(!is_null($dd12)){
                    $passengerUploadImages->user_upload_image = true;
                }else{
                    $passengerUploadImages->user_upload_image = false;
                }

            DB::commit();
            return $this->sendResponse('Image Upload Successfully',$passengerUploadImages,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function skipUploadImages(Request $request)
    {
        try{

            DB::beginTransaction(); 

            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
                'skip'       => 'required'
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);       
            }

            $clientlogin = $this::getCurrentClient(request());
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);

            $requests = RequestModel::where('id',$request->request_id)->first();

            $users_slug = User::where('id',$requests->user_id)->first();

            $slug = $users_slug->slug;

            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            if($user->hasRole('driver')){

                $requests = RequestModel::where('id',$request->request_id)->update([
                    'skip' => $request->skip
                ]);
            }

            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = "";
            $socket_data->result = ['skip_night_photo' => true];

            $socketData = ['event' => 'skip_photo_upload_'.$slug,'message' => $socket_data];
            sendSocketData($socketData);

            
            DB::commit();
            return $this->sendResponse('Skip Successfully',[],200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function retakeImage(Request $request)
    {
        try{

            DB::beginTransaction(); 

            $validator = Validator::make($request->all(), [
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

            $passengerUploadImages = PassengerUploadImages::where('request_id',$requests->id);

            $check_user = 0;
            $upload_status = false;
            $slug='';
            $check_driver = 0;
            $info_hash = 0;
            $mobile_application_type ='';
            if($user->hasRole('driver')){
                $passengerUploadImages = $passengerUploadImages->where('upload','USER');
                $upload_status = true;
                $user1 = User::where('id',$requests->user_id)->first();
                if(!is_null($user1)){
                    $slug = $user1->slug;
                    $info_hash = $user1->device_info_hash;
                    $mobile_application_type = $user1->mobile_application_type;
                }
            }
            if($user->hasRole('user')){
                $passengerUploadImages = $passengerUploadImages->where('upload','DRIVER');
                $upload_status = true;
                $user1 = User::where('id',$requests->driver_id)->first();
                if(!is_null($user1)){
                    $slug = $user1->slug;
                    $info_hash = $user1->device_info_hash;
                    $mobile_application_type = $user1->mobile_application_type;
                }
            }

            $passengerUploadImages = $passengerUploadImages->first();
            if(is_null($passengerUploadImages)) 
                return $this->sendError('Already Image deleted',[],404);

            deleteImage('images/passengers',$passengerUploadImages->image,'');
            $passengerUploadImages->delete();

            $title = Null;
            $body = '';
            $title = 'Photo not satisfied. Please retake image';
            $body = 'Photo not satisfied. Please retake image';
            $sub_title = 'Photo not satisfied. Please retake image';
             
            $pushData = ['upload_status' => $upload_status,'notification_enum' => PushEnum::UPLOAD_IMAGE_RETAKE];

            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = "";
            $socket_data->result = ['upload_status' => $upload_status];

            if($upload_status){
                $pushData['upload_image_url'] = $passengerUploadImages->images1;
                $socket_data->result['upload_image_url'] = $passengerUploadImages->images1;
                $socket_data->result['retake_image'] = true;

            }

            $socketData = ['event' => 'photo_upload_'.$slug,'message' => $socket_data];
            sendSocketData($socketData);
            dispatch(new SendPushNotification($title,$sub_title, $pushData, $info_hash, $mobile_application_type,0));

            DB::commit();
            return $this->sendResponse('Please retake image',[],200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
