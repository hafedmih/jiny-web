<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\RequestDedicatedDrivers;
use App\Models\User;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\Settings;
use App\Transformers\Request\TripRequestTransformer;
use App\Traits\CommanFunctions;
use DB;

class RequestDedicatedDriversController extends BaseController
{

    use CommanFunctions;

    protected $request;

    public function __construct(RequestDedicatedDrivers $request)
    {
        $this->request = $request;
    }

    public function RequestDedicated(Request $request)
    {
        $request->validate([
            'request_id' => 'required',
        ]);
        
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        $requestList = DB::table('request_dedicated_drivers')->join('users', 'users.id', '=', 'request_dedicated_drivers.driver_id')->join('drivers', 'drivers.user_id', '=', 'users.id')->join('vehicle', 'vehicle.id', '=', 'drivers.type')
        ->where('request_dedicated_drivers.request_id',$request->request_id)
        ->get(['drivers.car_number','drivers.car_model','users.firstname','users.lastname','users.profile_pic','users.slug','users.phone_number','vehicle.vehicle_name']);
        
        return $this->sendResponse('Data Found', $requestList, 200);
    }

    
  
}