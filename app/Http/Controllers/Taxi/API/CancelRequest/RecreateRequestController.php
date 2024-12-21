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
use App\Models\taxi\Vehicle;
use App\Models\User;
use App\Traits\CommanFunctions;
use phpseclib3\Crypt\EC\Formats\Keys\Common;
use Validator;
use App\Models\taxi\Requests\RequestDedicatedDrivers;
use App\Models\taxi\Requests\RequestPlace;
use DB;


class RecreateRequestController extends BaseController
{
    use CommanFunctions;

    public function Recreate(Request $request)
    {
    try {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);       
        }


        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        $request_detail = RequestModel::where('id', $request->request_id)->first();
        
        
           if (!$request_detail || $request_detail->is_completed == 1) {
            return $this->sendError('Request not found',[],401);
        }

        
        
        $request_result =    $request_detail->update([
            'is_cancelled'=>0,
            'cancelled_at'=> NULL,
            'cancel_method'=> NULL,
            'driver_id'=> NULL,
            'trip_start_time'=> now(),
            'arrived_at'=> NULL,
            'accepted_at'=> NULL,
            'completed_at'=> NULL,
            'is_driver_started'=> 0,
            'is_driver_arrived'=> 0,
            'is_trip_start'=> 0,
            'is_completed'=> 0,
            'total_distance'=> 0,
            'is_paid'=> 0
        ]);
        


     
        $request_place = RequestPlace::where('request_id',$request_detail->id)->first();

        if (!$request_place) {
            return $this->sendError('Request not found',[],401);
        }

        $type = Vehicle::where('slug', $request_detail->vehicle_type)->first();
        if (is_null($type)) {
            return $this->sendError('wrong Vechile Type', [], 403);
        }


        $selected_drivers = [];

        $drivers = fetchDrivers($request_place->pick_lat,$request_place->pick_lng,$request_detail->vehicle_type,$request_detail->ride_type);
        $drivers = json_decode($drivers->getContent());

        

        if ($drivers->success == true) {
            $noval = 0;
            foreach ($drivers->data as $key => $driver) {
                $driverdet = User::where('slug', $driver->id)->first();
                // $metta = RequestMeta::where('driver_id',$driverdet->id )->count();
                if (!is_null($driverdet)) {
                    $metta = RequestMeta::where(
                        'driver_id',
                        $driverdet->id == null ? ' ' : $driverdet->id
                    )->count();
                    // dd($driverdet->id);
                    if ($driverdet->active && $metta == 0) {
                        $selected_drivers[$noval]['user_id'] = $user->id;
                        $selected_drivers[$noval]['driver_id'] =
                            $driverdet->id;
                        $selected_drivers[$noval]['active'] =
                            $noval == 0 ? 1 : 0;
                        $selected_drivers[$noval]['request_id'] =
                            $request_detail->id;
                        $selected_drivers[$noval]['assign_method'] = 1;
                        $selected_drivers[$noval]['created_at'] = date(
                            'Y-m-d H:i:s'
                        );
                        $selected_drivers[$noval]['updated_at'] = date(
                            'Y-m-d H:i:s'
                        );
                        $noval++;
                    }
                    if($request_detail->is_later == 1){
                        $is_late = 1;
                    }else{
                        $is_late = 0;
                    }
                    RequestDedicatedDrivers::create([
                        'request_id' => $request_detail->id,
                        'user_id' => $user->id,
                        'driver_id' => $driver->id,
                        'assign_method' => 1,
                        'is_later' => $is_late,
                        'active' => 1
                    ]);
                    
                }
            }

            foreach ($selected_drivers as $key => $selected_driver) {

                $metaDriver = User::where(
                    'id',
                    $selected_driver['driver_id']
                )->first();
                $wallet = Wallet::where('user_id',$selected_driver['driver_id'])->where('balance_amount','>',settingValue('wallet_driver_minimum_balance_for_trip'))->first();

                if($metaDriver && $wallet){
                    $result = fractal($request_detail, new TripRequestTransformer());
                    // $result['request_number'] = $request_detail->request_number;

                    $title = null;
                    $body = '';
                    $lang = $metaDriver->language;

                    $push_data = $this->pushlanguage($lang, 'trip-created');
                    if (is_null($push_data)) {
                        $title = 'New Trip Requested 😊️';
                        $body =
                            'New Trip Requested, you can accept or Reject the request';
                        $sub_title =
                            'New Trip Requested, you can accept or Reject the request';
                    } else {
                        $title = $push_data->title;
                        $body = $push_data->description;
                        $sub_title = $push_data->description;
                    }

                    $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
                    // dd($pushData);
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message = PushEnum::REQUEST_CREATED;
                    $socket_data->result = $result;

                    $socketData = [
                        'event' => 'request_' . $metaDriver->slug,
                        'message' => $socket_data,
                    ];
                    sendSocketData($socketData);

                    // $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED, 'result' => (string)$result->toJson()];

                    // dd($metaDriver->mobile_application_type);
                    // dispatch(
                    //     new SendPushNotification(
                    //         $title,
                    //         $pushData,
                    //         $metaDriver->device_info_hash,
                    //         $metaDriver->mobile_application_type,
                    //         1,
                    //         $sub_title
                    //     )
                    // );
                    sendPush(
                        $title,
                        $sub_title,
                        $pushData,
                        $metaDriver->device_info_hash,
                        $metaDriver->mobile_application_type,
                        1
                    );

                    $request_meta = $request_detail->requestMeta()->create($selected_driver);

                    $request_meta = $request_detail->requestDedicatedDrivers()->create($selected_driver);

                }
            }

            
        } else {
            $request_detail->cancelled_at = NOW();
            $request_detail->is_cancelled = 1;
            $request_detail->cancel_method = 'Automatic';
            $request_detail->save();
            return $this->sendError(
                'No Driver Found',
                ['request_id' => $request_detail->id, 'error_code' => 2001],
                404
            );
        }



        return $this->sendResponse('Data Found', $request_detail, 200);
    } catch (\Exception $e) {
        DB::rollback(); 
        return $this->sendError('Catch error','failure.'.$e,400);  
    }
    }
}
