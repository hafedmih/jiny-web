<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Favourite;
use App\Models\taxi\UserComplaint;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\boilerplate\OauthClients;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Requests\RequestPlace;
use App\Models\boilerplate\CompanyDetails;
use App\Models\taxi\TripLogs;
use App\Models\taxi\Driver;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\Fine;
use DB;
use App\Models\User;
use App\Models\taxi\Notification;
use File;
use Validator;


class UserRemoveController extends BaseController
{

    public function userDelete(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            $user_request = RequestModel::where('user_id',$user->id);
            $user_complaint = UserComplaint::where('user_id',$user->id);
            $fav_list = Favourite::where('user_id',$user->id);
            $wallet = Wallet::where('user_id',$user->id);
            $walletTransaction = WalletTransaction::where('user_id',$user->id);
            $oauthClients = OauthClients::where('user_id',$user->id);
            $requestMeta = RequestMeta::where('user_id',$user->id);
            $requestRating = RequestRating::where('user_id',$user->id);
            $tripLogs = TripLogs::where('user_id',$user->id);
            $companyDetails = CompanyDetails::where('user_id',$user->id);
            $driver = Driver::where('user_id',$user->id);
            $driverDocument = DriverDocument::where('user_id',$user->id);
            $driverSubscriptions = DriverSubscriptions::where('user_id',$user->id);
            $notification = Notification::where('user_id',$user->id);
            $fine = Fine::where('user_id',$user->id);
            
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if(!is_null($user))
            {
                try{
                    $fav_list->forceDelete();
                    $user_request->forceDelete();
                    $user_complaint->forceDelete();
                    $wallet->forceDelete();
                    $oauthClients->forceDelete();
                    $requestMeta->forceDelete();
                    $requestRating->forceDelete();
                    $tripLogs->forceDelete();
                    $companyDetails->forceDelete();
                    $driverDocument->forceDelete();
                    $driver->forceDelete();
                    $user->forceDelete();
                    $walletTransaction->forceDelete();
                    $notification->forceDelete();
                    $fine->forceDelete();
                    $driverSubscriptions->forceDelete();
                    return $this->sendResponse('Data Found',[],200); 
    
                } catch (\Exception $e) {
                    return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
                }
            }
                else
                {
                return response()->json(['message' =>'failure.','error'=>'true'], 404);
                }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


   


   

   


}
