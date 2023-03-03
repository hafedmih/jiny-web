<?php

namespace App\Http\Controllers\Taxi\API\CancelRequest;

use App\Constants\CancelMethod;
use App\Constants\CancelType;
use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Taxi\API\CancellationRequest as CancellationTripRequest;
use App\Jobs\SendPushNotification;
use App\Models\taxi\CancellationRequest;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Requests\RequestDriverLog;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Settings;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\CommanFunctions;


class UserCancelRequestController extends BaseController
{
    use CommanFunctions;
    
    public function cancelRequest(CancellationTripRequest $request)
    {
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('user')) {
            return $this->sendError('Unauthorized',[],401);
        }

        $requestModel = RequestModel::where('id', $request->request_id)->first();

        if (!$requestModel || $requestModel->is_completed == 1 || $requestModel->is_cancelled == 1) {
            return $this->sendError('Request not found',[],401);
        }

        $requestModel->update([
            'is_cancelled'=>1,
            'cancelled_at'=>NOW(),
            'reason'=>$request->reason,
            'custom_reason'=>$request->custom_reason,
            'cancel_method'=>CancelMethod::USER_TEXT,
        ]);

        $user = $requestModel->userDetail;
        $user->trips_count = 0;
        $user->save();

        $cancellationFee = $this->calculateFee($requestModel);

        if ($requestModel->driver_id) {
            $cancelType = $requestModel->is_driver_arrived == 1 ? CancelType::AFTER_ARRIVED : CancelType::BEFORE_ARRIVE;
        } else {
            $cancelType = CancelType::BEFORE_ACCEPT;
        }

        $driver_accept = RequestDriverLog::where('request_id',$requestModel->id)->where('user_id',$user->id)->where('type','ACCEPT')->first();

        if ($requestModel->is_driver_started && $driver_accept && $driver_accept->driver_lat != "" && $driver_accept->driver_lng != "") {
            $travel_destance = $this->getDistance($driver_accept->driver_lat,$driver_accept->driver_lng,$request->driver_lat,$request->driver_lng);
            $cancel_fees_distance = Settings::where('name','cancel_fees_distance')->first();
            $cancel_fees_distance = $cancel_fees_distance ? $cancel_fees_distance->value : 0;
            if($travel_destance >= $cancel_fees_distance){
                $wallet = Wallet::where('user_id',$requestModel->driver_id)->first();
                if(!$wallet){
                    $wallet = new Wallet();
                    $wallet->user_id = $requestModel->driver_id;
                }
                $wallet->earned_amount +=  $cancellationFee;
                $wallet->balance_amount +=  $cancellationFee;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'request_id' => $requestModel->id,
                    'amount' => $cancellationFee,
                    'purpose' => 'Request Cancellation Fees',
                    'type' => 'EARNED',
                ]);
            }
            RequestDriverLog::create([
                'request_id' => $requestModel->id,
                'user_id' => $user->id,
                'driver_lat' => $request->driver_lat,
                'driver_lng' => $request->driver_lng,
                'date_time' => NOW(),
                'type' => 'CANCELLED',
                'user_type' => 'USER',
                'status' => 1
            ]);
            $distance = $this->getDistance($requestModel->requestPlace->pick_lat,$requestModel->requestPlace->pick_lng,$request->driver_lat,$request->driver_lng);
            $requestModel->cancellationRequest()->create([
                'reason'           => $request->reason,
                'custom_reason'    => $request->custom_reason,
                'cancellation_fee' => $cancellationFee,
                'cancelled_by'     => CancelMethod::USER_TEXT,
                'cancel_type'      => $cancelType,
                'user_lat'         => $request->user_lat,
                'user_lng'         => $request->user_lng,
                'driver_lat'       => $request->driver_lat,
                'driver_lng'       => $request->driver_lng,
                'user_location'    => $request->user_location,
                'driver_location'  => $request->driver_location,
                'distance'         => $distance
            ]);    
        }



        $driver = $requestModel->driverDetail;
        if (!$driver) {
            $request_meta_driver = $requestModel->requestMeta()->where('active', true)->first();
            if ($request_meta_driver) {
                $driver = $request_meta_driver->driver;
            } else {
                $driver = null;
            }
        }

        RequestMeta::where('request_id', $requestModel->id)->delete();

        $request_result =  fractal($requestModel, new TripRequestTransformer);

        if ($driver) {
            $driver->driver->is_available = true;
            $driver->driver->save();

            // Notify the driver that the user is cancelled the trip request

            $title = Null;
            $body = '';
            $lang = $driver->language;
            $push_data = $this->pushlanguage($lang,'trip-cancel');
            if(is_null($push_data)){
               $title = 'Trip Cancelled By Customer';
               $body = 'The customer cancelled the ride, please wait for another ride';
               $sub_title = 'The customer cancelled the ride, please wait for another ride';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 
           
            // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => (string)$request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_USER, 'result' => $request_result];
            
            $notifiable_driver = $driver;

            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_USER;
            $socket_data->result = $request_result;
            
            $socketData = ['event' => 'request_'.$notifiable_driver->slug,'message' => $socket_data];
            sendSocketData($socketData);

            dispatch(new SendPushNotification($title,$sub_title, $pushData, $notifiable_driver->device_info_hash, $notifiable_driver->mobile_application_type,0));
        }

        return $this->sendResponse('Data Found', $request_result, 200);
    }

    public function calculateFee($requestModel)
    {
        $price = ZonePrice::find($requestModel->zone_type_id);
        if(is_null($price))
            return 0;

        $cancellationFee = 0;

        if ($requestModel->is_later == 1) {
            $cancellationFee = $price->ridelater_cancellation_fee;
        }else{
            $cancellationFee = $price->ridenow_cancellation_fee;
        }

        return $cancellationFee;
    }
}