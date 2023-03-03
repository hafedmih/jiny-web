<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\API\UserProfileRequest;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\boilerplate\Country;
use DB;
use File;
use Validator;
use Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Kreait\Firebase\Factory;
use App\Traits\RandomHelper;


class CompanyController extends BaseController
{

    public function viewUser()
    {
        try{
            $companylist = User::role(['Company'])->get();
            if(is_null($companylist))
                return $this->sendError('No Data Found',[],404); 

                foreach ($companylist as $key => $value) {
                    $data['company'][$key]['id'] = $value->id;
                    $data['company'][$key]['slug'] = $value->slug;
                    $data['company'][$key]['firstname'] = $value->companyDetails->company_name; 
                    $data['company'][$key]['email'] = $value->email;
                    $data['company'][$key]['phone_number'] = $value->phone_number;
                }
            return $this->sendResponse('Data Found',$data,200);  
            // DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}

   

