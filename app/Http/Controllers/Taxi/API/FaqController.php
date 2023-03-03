<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Faq;
use DB;
use App\Models\User;
use File;
use Validator;
use App\Traits\RandomHelper;

class FaqController extends BaseController
{
    use RandomHelper;
    public function faqList(Request $request)
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
            
            // $faq = Faq::where('status',1)->get();
            // if(is_null($faq)){
            //     return $this->sendError('No Data Found',[],404);  
            // }
            // else{
            //     $data['faq'] = $faq;
                
            //     return $this->sendResponse('Data Found',$data,200);  
              
            // }
            if($user->hasRole('driver'))
            {
                $lang =  $request->header('Content-Language');
                $faq = $jsonString = [];
                if(File::exists(base_path('resources/lang/faq_'.$lang.'.json'))){
                    $jsonString = file_get_contents(base_path('resources/lang/faq_'.$lang.'.json'));
                    $jsonString = json_decode($jsonString, true);
                    $faq['faq'] =  [];
                
                    foreach($jsonString as $key => $json){
                        if($json['status'] == 1 && $json['category'] == 'driver' ) {
                             array_push($faq['faq'], $json);
                        }
    
                    } 
                    return $this->sendResponse('Data Found',$faq,200);  
                }
                else
                {
                    return $this->sendError('No Data Found',[],404);
                }
            }
            
               $lang =  $request->header('Content-Language');
                $faq = $jsonString = [];
                if(File::exists(base_path('resources/lang/faq_'.$lang.'.json'))){
                    $jsonString = file_get_contents(base_path('resources/lang/faq_'.$lang.'.json'));
                    $jsonString = json_decode($jsonString, true);
                    $faq['faq'] =  [];
                 
                    foreach($jsonString as $key => $json){
                        if($json['status'] == 1 && $json['category'] == 'user' ) {
                            array_push($faq['faq'], $json);
                       }
    
                    } 
                    return $this->sendResponse('Data Found',$faq,200);  
                }
                else
                {
                    return $this->sendError('No Data Found',[],404);
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    // public function test($slug){

    // }

}
