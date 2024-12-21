<?php
/*
    1. Check the Project Versioning  - Have to generate the Version code in the Admin pannel and that Code must hardcoded in the app Everytime App Open we have to validate the Code If code is not validate will not allow to enter the app
    2.Language - List all the available languages
    3.Country - List all the available country

*/

namespace App\Http\Controllers\boilerplate\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\boilerplate\Languages;
use App\Models\boilerplate\Country;
use App\Models\boilerplate\ProjectVersion;
use App\Models\taxi\Settings;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;
use File;
use Validator;


class LanguageTranslationAPIController extends BaseController
{
    /**
     * Get the languges Data 
     * @return Response
    */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'code' => 'required',
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);       
            }
            
            $projectVersion = ProjectVersion::where('version_code',$request->code)->where('status','OPEN')->first();
            if(is_null($projectVersion)){
                return $this->sendError('Please Update Application',[],426);  
            }
            $languages = Languages::where('status',1)->get();

            $places_api_key = Settings::where('name','google_map_key')->first();

            $distance_api_key = Settings::where('name','distance_matrix')->first();

            $geo_coder_api_key = Settings::where('name','geo_coder')->first();

            $directional_api_key = Settings::where('name','google_map_key')->first();

            if(is_null($languages)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $country = Country::where('status',1)->get();
                $s3_bucket_name = Settings::where('name','s3_bucket_name')->first();
                $s3_bucket_key = Settings::where('name','s3_bucket_key')->first();
                $s3_bucket_secret_access_key = Settings::where('name','s3_bucket_secret_access_key')->first();
                $s3_bucket_default_region = Settings::where('name','s3_bucket_default_region')->first();
                $data['languages'] = $languages;
                $data['country'] = $country;
                $data['s3_bucket_name'] = $s3_bucket_name ? $s3_bucket_name->value : NULL;
                $data['s3_bucket_key_id'] = $s3_bucket_key ? $s3_bucket_key->value : NULL;
                $data['s3_bucket_secret_access_key'] = $s3_bucket_secret_access_key ? $s3_bucket_secret_access_key->value : NULL;
                $data['s3_bucket_default_region'] = $s3_bucket_default_region ? $s3_bucket_default_region->value : NULL;
                $data['places_api_key']    = $places_api_key->value;
                $data['distance_api_key']  = $distance_api_key->value;
                $data['geo_coder_api_key'] = $geo_coder_api_key->value;
                $data['directional_api_key'] = $directional_api_key->value;


                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }   
    
    public function sendjsonfile($code){
        try{
            $jsonString = [];
            if(File::exists(base_path('public/lang/mob_'.$code.'.json'))){
                $jsonString = file_get_contents(base_path('public/lang/mob_'.$code.'.json'));
                $jsonString = json_decode($jsonString, true);
                return $this->sendResponse('Data Found',$jsonString,200);  
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
   
}