<?php

namespace App\Http\Controllers\Taxi\API\CancelRequest;

use App\Constants\CancelMethod;
use App\Constants\CancelType;
use App\Constants\PushEnum;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Taxi\API\CancellationRequest as CancellationTripRequest;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Transformers\Request\TripRequestTransformer;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\RequestDriverLog;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Settings;
use App\Models\User;
use App\Traits\CommanFunctions;
use phpseclib3\Crypt\EC\Formats\Keys\Common;

class DriverCancelRequestController extends BaseController
{
    use CommanFunctions;

    public function cancelRequest(CancellationTripRequest $request)
    {
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        if (!$user->hasRole('driver')) {
            return $this->sendError('Unauthorized',[],401);
        }

        $requestModel = RequestModel::where('id', $request->request_id)->first();

        if (!$requestModel || $requestModel->is_completed == 1 || $requestModel->is_cancelled == 1) {
            return $this->sendError('Request not found',[],401);
        }

        $driver = $user->driver;
        $driver->is_available = true;
        $driver->save();

        $requestModel->update([
            'is_cancelled'=>1,
            'cancelled_at'=>NOW(),
            'reason'=>$request->reason,
            'custom_reason'=>$request->custom_reason,
            'cancel_method'=>CancelMethod::DRIVER_TEXT,
        ]);
        $driver = $requestModel->driverDetail;
        $driver->trips_count = 0;
        $driver->save();

        $cancellationFee = (new UserCancelRequestController())->calculateFee($requestModel);

    // dd($cancellationFee);
    // dd($requestModel->requestPlace);
        $driver_accept = RequestDriverLog::where('request_id',$requestModel->id)->where('user_id',$user->id)->where('type','ACCEPT')->first();

        if($driver_accept && $driver_accept->driver_lat != "" && $driver_accept->driver_lng){
            $travel_destance = $this->getDistance($driver_accept->driver_lat,$driver_accept->driver_lng,$request->driver_latitude,$request->driver_longitude);
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
                    'user_id' => $requestModel->driver_id,
                    'purpose' => 'Request Cancellation Fees',
                    'type' => 'EARNED',
                ]);
            }
        }
        RequestDriverLog::create([
            'request_id' => $requestModel->id,
            'user_id' => $user->id,
            'driver_lat' => $request->driver_lat,
            'driver_lng' => $request->driver_lng,
            'date_time' => NOW(),
            'type' => 'CANCELLED',
            'user_type' => 'DRIVER',
            'status' => 1
        ]);
        $cancelType = $requestModel->is_driver_arrived == 1 ? CancelType::AFTER_ARRIVED : CancelType::BEFORE_ARRIVE;
        $distance = $this->getDistance($requestModel->requestPlace->pick_lat,$requestModel->requestPlace->pick_lng,$request->driver_latitude,$request->driver_longitude);
        $requestModel->cancellationRequest()->create([
            'reason'           => $request->reason,
            'custom_reason'    => $request->custom_reason,
            'cancellation_fee' => $cancellationFee,
            'cancelled_by'     => CancelMethod::DRIVER_TEXT,
            'cancel_type'      => $cancelType,
            'user_lat'         => $request->user_lat,
            'user_lng'         => $request->user_lng,
            'driver_lat'       => $request->driver_latitude,
            'driver_lng'       => $request->driver_longitude,
            'user_location'    => $request->user_location,
            'driver_location'  => $request->driver_location,
            'distance'         => $distance
        ]);

        $user = $requestModel->userDetail;

        $request_result =  fractal($requestModel, new TripRequestTransformer);

        if ($user) {

            $title = Null;
            $body = '';
            $lang = $user->language;
            $push_data = $this->pushlanguage($lang,'trip-driver-cancel');
            if(is_null($push_data)){
                $title = 'Trip Cancelled By Driver';
                $body = 'The driver cancelled the ride, please create another ride';
                $sub_title = 'The driver cancelled the ride, please create another ride';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            } 


            // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER, 'result' => (string)$request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER];

            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DRIVER;
            $socket_data->result = $request_result;
            
            $socketData = ['event' => 'request_'.$user->slug,'message' => $socket_data];
            sendSocketData($socketData);

            dispatch(new SendPushNotification($title,$sub_title, $pushData, $user->device_info_hash, $user->mobile_application_type,0));
        }

        return $this->sendResponse('Data Found', $request_result, 200);
    }
}