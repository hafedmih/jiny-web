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
use App\Traits\RandomHelper;



class checkotpController extends BaseController
{

    use RandomHelper;
    public function checkotp(Request $request)
    {

        $request_otp = $this->UniqueRandomNumbers(4); 

        // dd($request_otp);
        
        // dd($request_otp);

        
    }



}
