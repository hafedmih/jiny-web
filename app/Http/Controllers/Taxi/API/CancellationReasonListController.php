<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\CancellationReason;
use App\Transformers\CancellationReasonTransformer;
use Illuminate\Http\Request;
use App\Models\User;

class CancellationReasonListController extends BaseController
{
    public function cancellationReason(Request $request)
    {
        
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);
        
        $role = $user->hasRole('user') ? ['user','both'] : ['driver','both'];
        // dd($role);
        $reason = CancellationReason::whereIn('user_type',$role);
        // dd($reason);
        if ($request->has('accept_status')) {
            $reason = $reason->where('trip_status','Before Arrive');
        }

        if ($request->has('arrive_status')) {
            $reason = $reason->where('trip_status','After Arrived');
        }

        $reasons = $reason->get();
        $result['reasons'] = $reasons;
        // $result = fractal($reasons, new CancellationReasonTransformer);
        
        return $this->sendResponse('Data Found', $result, 200);
    }
}
