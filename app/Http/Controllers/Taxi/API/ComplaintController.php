<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Complaint;
use App\Models\taxi\Tripcomplaint;
use App\Models\taxi\UserComplaint;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use DB;
use File;
use Validator;

class ComplaintController extends BaseController
{
    public function complaintsList(Request $request)
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
            
            if($user->hasRole('user'))
            {
                $lang =  $request->header('Content-Language');
                $complaint = Complaint::where('type','user')->where('status',1)->where('language',$lang)->get();
                if(is_null($complaint)){
                    return $this->sendError('No Data Found',[],404);  
                }
                else{
                    $data['complaint'] = $complaint;
                    return $this->sendResponse('Data Found',$data,200);  
                }

                // $lang =  $request->header('Content-Language');
                
                // $com = $jsonString = [];
                // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
                //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
                //     $jsonString = json_decode($jsonString, true);
                //     $com['complaint'] =  [];
                
                //     foreach($jsonString as $key => $json){
                //         if($json['category'] == 1 && $json['status'] == 1 && $json['type'] == 'user') {
                //             array_push($com['complaint'], $json);
                //         }
                //     }
              
                //     if(is_null($com)){
                //             return $this->sendError('No Data Found',[],404);  
                //         }
                //         else{
                //             $data = $com;
                //             return $this->sendResponse('Data Found',$data,200);
                //         }
                // }
            }
                $lang =  $request->header('Content-Language');
                $complaint = Complaint::where('type','driver')->where('status',1)->where('language',$lang)->get();
                if(is_null($complaint)){
                    return $this->sendError('No Data Found',[],404);  
                }
                else{
                    $data['complaint'] = $complaint;
                return $this->sendResponse('Data Found',$data,200); 
                // $lang =  $request->header('Content-Language');
                // $com = $jsonString = [];
                // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
                //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
                //     $jsonString = json_decode($jsonString, true);
                //     $com['complaint'] =  [];    

                    // foreach($jsonString as $key => $json){
                    //     if($json['category'] == 1 && $json['status'] == 1 && $json['type'] == 'driver' && $json['complaint_type'] == 1) {
                    //         array_push($com['complaint'], $json);
                    //     }
                    // }
                    // if(is_null($com)){
                    //     return $this->sendError('No Data Found',[],404);  
                    // }
                    // else{
                    //     $data = $com;
                    // return $this->sendResponse('Data Found',$data,200);  
                // }
            }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    public function tripComplaintsList(Request $request)
    {
        
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == 0)
                return $this->sendError('User is blocked so please contact admin',[],403);
            
            if($user->hasRole('user'))
            {
                $lang =  $request->header('Content-Language');
                $complaint = Tripcomplaint::where('status',1)->where('language',$lang)->where('type','user')->get();
                if(is_null($complaint)){
                    return $this->sendError('No Data Found',[],404);  
                }
                else{
                    $data['complaint'] = $complaint;
                    return $this->sendResponse('Data Found',$data,200);  
                }
                // $lang =  $request->header('Content-Language');
                
                // $com = $jsonString = [];
                // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
                //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
                //     $jsonString = json_decode($jsonString, true);
                //     $com['complaint'] =  [];    
    
                //     foreach($jsonString as $key => $json){
                //         if($json['category'] == 1 && $json['status'] == 1 && $json['type'] == 'user' && $json['complaint_type'] == 2) {
                //             array_push($com['complaint'], $json);
                //         }
                //     }
                   
                
                //     if(is_null($com)){
                //         return $this->sendError('No Data Found',[],404);  
                //     }
                //     else{
                //         $data = $com;
                //     return $this->sendResponse('Data Found',$data,200);  
                //     }
                }
            // $complaint = Complaint::where('category',1)->where('status',1)->where('type','driver')->get();
            $lang =  $request->header('Content-Language');
                $complaint = Tripcomplaint::where('status',1)->where('language',$lang)->where('type','driver')->get();
                if(is_null($complaint)){
                    return $this->sendError('No Data Found',[],404);  
                }
                else{
                    $data['complaint'] = $complaint;
                    return $this->sendResponse('Data Found',$data,200);  
                }
                // $lang =  $request->header('Content-Language');
                // $com = $jsonString = [];
                // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
                //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
                //     $jsonString = json_decode($jsonString, true);
                //     $com['complaint'] =  [];    

                //     foreach($jsonString as $key => $json){
                //         if($json['status'] == 1 && $json['type'] == 'driver' && $json['category'] == 1) {
                //             array_push($com['complaint'], $json);
                //         }
                //     }
                //     if(is_null($com)){
                //         return $this->sendError('No Data Found',[],404);  
                //     }
                //     else{
                //         $data = $com;
                //     return $this->sendResponse('Data Found',$data,200);  
                //     }
                // }

            // }else{
                return $this->sendError('Dispute is not available for drivers',[],404);  
            // }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function complaintsUserAdd(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'complaint_id' => 'required',
                'answer' => 'required'
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

            $data = $request->all();
            if($request->complaint_id)
            {
                $comp = Complaint::where('slug',$request->complaint_id)->where('status',1)->first();
                $trip = Tripcomplaint::where('slug',$request->complaint_id)->where('status',1)->first();
            
                if(!is_null($comp))
                {
                    
                    $user = UserComplaint::create([
                        'answer' => $data['answer'],
                        'user_id' => $user->id,
                        'complaint_id' => $comp->id,
                        'category' => $comp->category,
                        'request_id' => $request->request_id ? $data['request_id'] : '',
                        'status' => 1
                    ]);
        
                    return $this->sendResponse('User Complaint Added Successfully!...',$user,200);
        
                    DB::commit();
                }
                elseif(!is_null($trip))
                {
                    $user = UserComplaint::create([
                        'answer' => $data['answer'],
                        'user_id' => $user->id,
                        'tripcomplaint_id' => $trip->id,
                        'category' => $trip->category,
                        'request_id' => $request->request_id ? $data['request_id'] : '',
                        'status' => 1
                    ]);
        
                    return $this->sendResponse('User Complaint Added Successfully!...',$user,200);
        
                    DB::commit();
                }
                else{
                    return $this->sendError('Invalid Complaint',[],403); 
                }
            }
        
            // if(($request->has('request_id')))
            // {
            //     $complaint = Tripcomplaint::where('slug',$data['slug'])->where('status',1)->first();
            //     if(is_null($complaint))
            //     {
            //         return $this->sendError('Invalid Complaint',[],403); 
            //     }
            //     $user = UserComplaint::create([
            //         'answer' => $data['answer'],
            //         'user_id' => $user->id,
            //         'tripcomplaint_id' => $complaint->id,
            //         'category' => $complaint->category,
            //         'request_id' => $request->request_id ? $data['request_id'] : '',
            //         'status' => 1
            //     ]);

            //     return $this->sendResponse('User Complaint Added Successfully!...',$user,200);

            //     DB::commit();
            // }else
            // {
            //     $complaint = Complaint::where('slug',$data['slug'])->where('status',1)->first();
            //     if(is_null($complaint))
            //     {
            //         return $this->sendError('Invalid Complaint',[],403);  
            //     }
            //     $user = UserComplaint::create([
            //         'answer' => $data['answer'],
            //         'user_id' => $user->id,
            //         'complaint_id' => $complaint->id,
            //         'category' => $complaint->category,
            //         'request_id' => $request->request_id ? $data['request_id'] : '',
            //         'status' => 1
            //     ]);
    
            //     return $this->sendResponse('User Complaint Added Successfully!...',$user,200);
    
            //     DB::commit();
            // }
            

            // $user = UserComplaint::create([
            //     'answer' => $data['answer'],
            //     'user_id' => $user->id,
            //     'complaint_id' => $complaint->id,
            //     'category' => $complaint->category,
            //     'request_id' => $request->request_id ? $data['request_id'] : '',
            //     'status' => 1
            // ]);

            // return $this->sendResponse('User Complaint Added Successfully!...',$user,200);

            // DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); 
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function suggestionList(Request $request)
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
            
            if($user->hasRole('user'))
            {
                $lang =  $request->header('Content-Language');
                $complaint = Complaint::where('status',1)->where('language',$lang)->where('type','user')->get();
                if(is_null($complaint)){
                    return $this->sendError('No Data Found',[],404);  
                }
                else{
                    $data['suggestion'] = $complaint;
                    return $this->sendResponse('Data Found',$data,200);  
                }
                // $lang =  $request->header('Content-Language');
                // $com = $jsonString = [];
                // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
                //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
                //     $jsonString = json_decode($jsonString, true);
                //     $com['suggestion'] =  [];
                
                //     foreach($jsonString as $key => $json){
                //         if( $json['category'] == 2 && $json['status'] == 1 && $json['type'] == 'user') {
                //             array_push($com['suggestion'], $json);
                //         }
                //     }
              
                //     if(is_null($com)){
                //             return $this->sendError('No Data Found',[],404);  
                //         }
                //         else{
                //             $data = $com;
                //             return $this->sendResponse('Data Found',$data,200);
                //         }
                // }
            }
            $lang =  $request->header('Content-Language');
            $complaint = Complaint::where('status',1)->where('language',$lang)->where('type','driver')->get();
            // $lang =  $request->header('Content-Language');
            // $complaint = $jsonString = [];
            // if(File::exists(base_path('resources/lang/com_'.$lang.'.json'))){
            //     $jsonString = file_get_contents(base_path('resources/lang/com_'.$lang.'.json'));
            //     $jsonString = json_decode($jsonString, true);
            //     $complaint['suggestion'] =  [];    

            //     foreach($jsonString as $key => $json){
            //         if($json['status'] == 1 && $json['type'] == 'driver' && $json['category'] == 2) {
            //             array_push($complaint['suggestion'], $json);
            //         }
            //     }
            if(is_null($complaint)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data['suggestion'] = $complaint;
                return $this->sendResponse('Data Found',$data,200);  
            }
        // }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function userComplaintsList(Request $request,$slug)
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
            
            // if($user->hasRole('user'))
            // {
            //     $lang =  $request->header('Content-Language');
                
            // }
            $lang =  $request->header('Content-Language');

            $data = [];
            if($slug == 'suggestion'){
                $suggestion = UserComplaint::where('user_id',$user->id)->where('status',1)->where('category',2)->get();

                foreach ($suggestion as $key => $value) {
                    if($value->tripcomplaint_id != ''){
                        $suggestion[$key]->suggestion_type = 1;
                        $suggestion[$key]->date = date("Y-m-d H:i:s", strtotime($value->created_at));
                        $suggestion[$key]->title = $value->tripComplaints->title;
                        $suggestion[$key]->type = $value->tripComplaints->type;
                        $suggestion[$key]->slug = $value->tripComplaints->slug;
                        $suggestion[$key]->language = $value->tripComplaints->language;
                    }else{
                        $suggestion[$key]->suggestion_type = 0;
                        $suggestion[$key]->date = date("Y-m-d H:i:s", strtotime($value->created_at));
                        $suggestion[$key]->title = $value->complaintDetails->title;
                        $suggestion[$key]->type = $value->complaintDetails->type;
                        $suggestion[$key]->slug = $value->complaintDetails->slug;
                        $suggestion[$key]->language = $value->complaintDetails->language;
                    }
                }
                $data['suggestion'] = $suggestion;
                if(count($suggestion) == 0){
                    return $this->sendError('No Data Found',[],404);  
                }
            }
            elseif($slug == 'complaints'){
                $complaint = UserComplaint::where('user_id',$user->id)->where('status',1)->where('category',1)->get();
                
                foreach ($complaint as $key => $value) {
                    if($value->tripcomplaint_id != ''){
                        $complaint[$key]->complaint_type = 1;
                        $complaint[$key]->date = date("Y-m-d H:i:s", strtotime($value->created_at));
                        $complaint[$key]->title = $value->tripComplaints->title;
                        $complaint[$key]->type = $value->tripComplaints->type;
                        $complaint[$key]->slug = $value->tripComplaints->slug;
                        $complaint[$key]->language = $value->tripComplaints->language;
                    }else{
                        $complaint[$key]->complaint_type = 0;
                        $complaint[$key]->date = date("Y-m-d H:i:s", strtotime($value->created_at));
                        $complaint[$key]->title = $value->complaintDetails->title;
                        $complaint[$key]->type = $value->complaintDetails->type;
                        $complaint[$key]->slug = $value->complaintDetails->slug;
                        $complaint[$key]->language = $value->complaintDetails->language;
                    }
                }
                $data['complaints'] = $complaint;
                if(count($complaint) == 0){
                    return $this->sendError('No Data Found',[],404);  
                }
            }
            else{
                return $this->sendError('No Data Found',[],404);
            }
            
            return $this->sendResponse('Data Found',$data,200);  
        // }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}
