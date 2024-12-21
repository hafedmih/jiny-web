<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Taxi\API\StoreSosRequest;
use App\Models\taxi\Sos;
use DB;
use App\Models\User;
use File;
use Validator;

class SosController extends BaseController
{
    /**
     * Validate client exists or valid client 
     *  #header @param bearerToken
    */
    public function validateClient()
    {
        $clientlogin = $this::getCurrentClient(request());
      
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        return $user;
    }

    /**
     * sos list 
     * #header @param bearerToken
    */
    public function sosList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
        
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);
            
            
            // $sos = $this->getSosData($user);

            // if(is_null($sos)){
            //     return $this->sendError('No Data Found',[],404);  
            // }
            // else{
            //     $data['sos'] = $sos;
            //     return $this->sendResponse('Data Found',$data,200);  
            // }
            $lang =  $request->header('Content-Language');
            $sos = $jsonString = [];
            if(File::exists(base_path('resources/lang/sos_'.$lang.'.json'))){
                $jsonString = file_get_contents(base_path('resources/lang/sos_'.$lang.'.json'));
                $jsonString = json_decode($jsonString, true);
                $sos['sos'] =  [];
            
                foreach($jsonString as $key => $json){
                    if($json['created_by'] == $user->id || $json['created_by'] == Null) {
                        array_push($sos['sos'], $json);
                    }
                }
          
                if(is_null($sos)){
                        return $this->sendError('No Data Found',[],404);  
                    }
                else{
                    $data = $sos;
                    return $this->sendResponse('Data Found',$data,200);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    /**
     * store sos data 
     * #header @param bearerToken
     * @param title
     * @param description
     * @param phone_number
    */
    public function store(StoreSosRequest $request)
    {
        DB::beginTransaction();

        try {
            $clientlogin = $this::getCurrentClient(request());
        
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

            $params = $request->only(['title','phone_number','description','language']);
            $params['created_by'] = $user->id;
    
            
            $sosData =Sos::create($params);
            $sosData['status'] = 1;
            $lang =  $request->header('Content-Language');
           
            // $sosData = $this->getSosData($user);
            //   dd($sosData);
            $data = $this->openJSONFile($lang);
        
            $data[$sosData->slug] = $sosData; 
            $this->saveJSONFile($lang, $data);
            DB::commit();
            return $this->sendResponse('SOS Added Successfully!..',$sosData,200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    /**
     * update sos data 
     * --header bearerToken
     * @param title
     * @param description
     * @param phone_number
    */
    public function update(StoreSosRequest $request,Sos $sos)
    {
        DB::beginTransaction();

        try {
            $clientlogin = $this::getCurrentClient(request());
        
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

            $params = $request->only(['title','phone_number','description']);
    
            $sos->update($params);
            $sosData = $this->getSosData($user);

            DB::commit();
            return $this->sendResponse('SOS Updated Successfully!..',$sosData,200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    /**
     * Delete sos 
     * 
    */
    public function delete(Sos $sos,$slug)
    {
        DB::beginTransaction();

        try {
            $clientlogin = $this::getCurrentClient(request());
          //  dd($clientlogin);
            if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);
    
            $user = User::find($clientlogin->user_id);
            if(is_null($user)) return $this->sendError('Unauthorized',[],401);
            //dd($slug);
            if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

            // if ($sos->created_by != $user->id) {
                
            //     return $this->sendError('Unable to delete sos',[],400);
            // }

            $sos->delete();

            // $sosData = $this->getSosData($user);
            $languages = DB::table('languages')->get();

            if($languages->count() > 0){
                foreach ($languages as $language){
                     $data = $this->openJSONFile($language->code);
                            unset($data[$slug]);
                      $this->saveJSONFile($language->code, $data);
                }
                
            }

            DB::commit();
            return $this->sendResponse('SOS Deleted Successfully!..',$sos,200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    /**
     * fetch sos data 
     * 
    */
    public function getSosData($user)
    {
        return Sos::where('status',1)->where(function($q) use ($user){
            $q->whereNull('created_by')->orWhere('created_by',$user->id);
        })->get();
    }

    private function openJSONFile($code){
        $jsonString = [];
        if(File::exists(base_path('resources/lang/sos_'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/sos_'.$code.'.json'));
            $jsonString = json_decode($jsonString, true);
        }
        return $jsonString;
    }
    /**
     * Save JSON File
     * @return Response
    */
    private function saveJSONFile($code, $data){
        ksort($data);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path('resources/lang/sos_'.$code.'.json'), stripslashes($jsonData));
       
       
    }
}
