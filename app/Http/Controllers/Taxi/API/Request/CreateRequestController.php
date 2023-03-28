<?php

namespace App\Http\Controllers\Taxi\API\Request;

use App\Constants\PushEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Taxi\API\Request\CreateTripRequest;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestMeta;
use App\Models\User;
use App\Traits\CommanFunctions;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Wallet;
use App\Models\taxi\UserInstantTrip;

use App\Models\taxi\Promocode;
use DB;
// use Kreait\Firebase\Database;
use App\Traits\RandomHelper;
use App\Models\boilerplate\OauthClients;

class CreateRequestController extends BaseController
{
    use CommanFunctions, RandomHelper;

    public $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    public function createRequest(CreateTripRequest $request)
    {
        // dd($request->trip_way_type);

        try {
            $clientlogin = $this::getCurrentClient(request());

            if (is_null($clientlogin)) {
                return $this->sendError('Token Expired', [], 401);
            }

            $user = User::find($clientlogin->user_id);
            if (is_null($user)) {
                return $this->sendError('Unauthorized', [], 401);
            }

            if ($user->active == false) {
                return $this->sendError(
                    'User is blocked so please contact admin',
                    [],
                    403
                );
            }

            // Check if the user has registred a trip already
            // $this->validateUserInTrip($user);
            // Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
            // $this->validateUserRequestedTrip($user);
            // Validate payment option for the trip
            $paymentOpt = $this->validatePaymentOption($request);

            if ($request->has('ride_type') && $request->ride_type == 'RENTAL') {
                return (new RentalController())->createRideRental(
                    $request,
                    $user
                );
            }

            if (
                $request->has('ride_type') &&
                $request->ride_type == 'OUTSTATION'
            ) {
                return (new OutstationCreateController())->rideOutstation(
                    $request,
                    $user
                );
            }
            // find zone
            $type = Vehicle::where('slug', $request->vehicle_type)->first();
            if (is_null($type)) {
                return $this->sendError('wrong Vechile Type', [], 403);
            }

            $zone = $this->getZone($request->pick_lat, $request->pick_lng);
            //  dd($zone);

            if (!$zone) {
                return $this->sendError(
                    'Service not available at this location',
                    [],
                    403
                );
            }

            $zone_type_id = 0;
            foreach ($zone->getZonePrice as $zoneprice) {
                if ($zoneprice->type_id == $type->id) {
                    $zone_type_id = $zoneprice->id;
                }
            }
            if ($zone_type_id == 0) {
                return $this->sendError(
                    'Service not available at this location',
                    [],
                    403
                );
            }

            // payment_types
            if ($request->has('payment_opt')) {
                $payment = explode(',', $zone->payment_types);
                $value = 0;
                foreach ($payment as $option) {
                    if ($option == $request->payment_opt) {
                        $value = 1;
                    }
                }
                if ($value == 0) {
                    return $this->sendError(
                        'Payment Option is not avaialble. Plesae select another option',
                        [],
                        403
                    );
                }
            }

            if ($request->has('is_later')) {
                return (new CreateRideLaterController())->rideLater(
                    $request,
                    $zone,
                    $user,
                    $zone_type_id
                );
            }

            if ($request->has('is_instant')) {
                $requestNumber = generateRequestNumber();
                $request_otp = $this->UniqueRandomNumbers(4);
                $request_params = [
                    'request_number' => $requestNumber,
                    'request_otp' => $request_otp,
                    'zone_type_id' => $zone_type_id,
                    'payment_opt' => $request->payment_opt,
                    'unit' => $zone->unit,
                    'requested_currency_code' =>
                        $zone->getCountry->currency_code,
                    'requested_currency_symbol' =>
                        $zone->getCountry->currency_symbol,
                    'driver_info' => $request->driver_notes,
                    'driver_id' => $user->id,
                    'is_driver_started' => 1,
                    'is_driver_arrived' => 1,
                    'is_instant_trip' => 1,
                    'is_trip_start' => 1,
                    'trip_start_time' => NOW(),
                    'accepted_at' => NOW(),
                    'arrived_at' => NOW(),
                    'trip_type' => $request->ride_type,
                    'driver_notes' => $request->driver_notes,
                ];

                $request_detail = $this->request->create($request_params);
                $instant_user_id = null;
                $userModel = User::where(
                    'phone_number',
                    'like',
                    '%' . $request->phone_number
                )
                    ->role('user')
                    ->first();
                if ($userModel) {
                    $instant_user_id = $userModel->id;
                } else {
                    $user_instant = new UserInstantTrip();
                    $user_instant->request_id = $request_detail->id;
                    $user_instant->firstname =
                        $request->firstname == null ? ' ' : $request->firstname;
                    $user_instant->lastname =
                        $request->lastname == null ? ' ' : $request->lastname;
                    $user_instant->email =
                        $request->email == null ? ' ' : $request->email;
                    $user_instant->phone_number =
                        $request->phone_number == null
                            ? ' '
                            : $request->phone_number;
                    $user_instant->save();

                    $instant_user_id = $user_instant->id;
                }

                $request_detail->update(['user_id' => $instant_user_id]);

                if ($request->has($request->drop_lat)) {
                    $request_place_params = [
                        'pick_lat' => $request->pick_lat,
                        'pick_lng' => $request->pick_lng,
                        'drop_lat' => $request->drop_lat,
                        'drop_lng' => $request->drop_lng,
                        'pick_address' => $request->pick_address,
                        'drop_address' => $request->drop_address,
                        'poly_string' => $request->poly_string,
                        'driver_notes' => $request->driver_notes,
                    ];
                } else {
                    $request_place_params = [
                        'pick_lat' => $request->pick_lat,
                        'pick_lng' => $request->pick_lng,
                        'pick_address' => $request->pick_address,
                        'drop_address' => $request->drop_address,
                        'poly_string' => $request->poly_string,
                    ];
                }

                if ($request->has($request->drop_lat)) {
                    // request history detail params
                    $request_history_params = [
                        'olat' => $request->pick_lat,
                        'olng' => $request->pick_lng,
                        'dlat' => $request->drop_lat,
                        'dlng' => $request->drop_lng,
                        'pick_address' => $request->pick_address,
                        'drop_address' => $request->drop_address,
                    ];
                } else {
                    $request_history_params = [
                        'olat' => $request->pick_lat,
                        'olng' => $request->pick_lng,
                        'pick_address' => $request->pick_address,
                    ];
                }

                $request_detail->requestPlace()->create($request_place_params);
                $request_detail
                    ->requestHistory()
                    ->create($request_history_params);
                DB::commit();
                $request_result = fractal(
                    $request_detail,
                    new TripRequestTransformer()
                );
                if ($userModel) {
                    $push_request_detail = $request_result->toJson();
                    $userModel = User::find($request_detail->user_id);
                    $title = null;
                    $body = '';
                    $lang = $userModel->language;
                    $push_data = $this->pushlanguage($lang, 'trip-created');
                    if (is_null($push_data)) {
                        $title = 'New Trip Created';
                        $body = 'Enjoy your Trip with us !!';
                        $sub_title = 'Enjoy your Trip with us !!';
                    } else {
                        $title = $push_data->title;
                        $body = $push_data->description;
                        $sub_title = $push_data->description;
                    }

                    $push_data = [
                        'notification_enum' =>
                            PushEnum::DRIVER_STARTED_THE_TRIP,
                        'result' => (string) $push_request_detail,
                    ];
                    // dd($push_data);
                    // Form a socket sturcture using users'id and message with event name
                    $socket_data = new \stdClass();
                    $socket_data->success = true;
                    $socket_data->success_message =
                        PushEnum::DRIVER_STARTED_THE_TRIP;
                    $socket_data->result = $request_result;

                    $socketData = [
                        'event' => 'request_' . $userModel->slug,
                        'message' => $socket_data,
                    ];
                    sendSocketData($socketData);

                    // $pushData = ['notification_enum' => PushEnum::DRIVER_STARTED_THE_TRIP, 'result' => (string) $request_result->toJson()];
                    $pushData = [
                        'notification_enum' =>
                            PushEnum::DRIVER_STARTED_THE_TRIP,
                        'result' => $request_result,
                    ];
                    dispatch(
                        new SendPushNotification(
                            $title,
                            $sub_title,
                            $pushData,
                            $userModel->device_info_hash,
                            $userModel->mobile_application_type,
                            0
                        )
                    );
                }

                return $this->sendResponse('Data Found', $request_result, 200);
            }

            $promocode_id = 0;
            if (request()->has('promo_code')) {
                $promocode = Promocode::whereStatus(true)
                    ->where('promo_code', $request['promo_code'])
                    ->first();
                if (is_null($promocode)) {
                    return $this->sendError('Wrong Promo Code', [], 403);
                }

                $promocode_id = $promocode->id;

                $promo_count = $this->request
                    ->where('promo_id', $promocode_id)
                    ->where('user_id', $user->id)
                    ->where('is_completed', 1)
                    ->count();
                if ($promo_count > $promocode->promo_user_reuse_count) {
                    return $this->sendError(
                        'Sorry! You already ' .
                            $promocode->promo_user_reuse_count .
                            ' times used this promo code',
                        [],
                        403
                    );
                }

                $promo_all_count = $this->request
                    ->where('promo_id', $promocode_id)
                    ->where('is_completed', 1)
                    ->count();
                if ($promo_all_count > $promocode->promo_use_count) {
                    return $this->sendError('Sorry! promo code exit', [], 403);
                }

                // if (!in_array($type->id, $promocode->types)) {
                //     return $this->sendError('Sorry! promo code exit', [], 403);
                // }
            }
            $requestNumber = generateRequestNumber();
            // dd($requestNumber);
            $request_otp = $this->UniqueRandomNumbers(4);
            //Booking for by deena
            $booking_for = $request->booking_for;
            // dd($booking_for);
            $request_params = [
                'request_number' => $requestNumber,
                'request_otp' => $request_otp, //rand(1111, 9999),
                'user_id' => $user->id,
                'zone_type_id' => $zone_type_id,
                'payment_opt' => $request->payment_opt,
                'unit' => $zone->unit,
                'promo_id' => $promocode_id,
                'requested_currency_code' => $zone->getCountry->currency_code,
                'requested_currency_symbol' =>
                    $zone->getCountry->currency_symbol,
                'driver_info' => $request->driver_notes,
                'trip_type' => $request->ride_type,
                'driver_notes' => $request->driver_notes,
                'booking_for' => $booking_for,
                'trip_start_time' => NOW(),
                'destination_type' => $request->has('drop_address') && $request->has('drop_lat') && $request->has('drop_lng') ? 'NORMAL' : 'OPEN',
                'amount' => $request->trip_amount
            ];
            //    dd($request_params);
            $request_detail = $this->request->create($request_params);

            $other_user = '';
            if ($booking_for == 'OTHERS') {
                $others_user = User::create([
                    'firstname' => $request['others_name'],
                    'phone_number' => $request['others_number'],
                ]);

                $request_param = [
                    'others_user_id' => $others_user->id,
                ];
                $request_details = RequestModel::where(
                    'request_number',
                    $request_detail->request_number
                )->update(['others_user_id' => $others_user->id]);

                $user->assignRole('user');
                $client = new OauthClients();
                $client->user_id = $user->id;
                $client->name = $others_user->firstname;
                $client->secret = $this->generateRandomString(40);
                $client->redirect = 'http://localhost';
                $client->personal_access_client = false;
                $client->password_client = false;
                $client->revoked = false;
                $client->save();
            }

            if ($request->has('stops')) {
                $stops = json_decode($request->stops);
                // dd(count($stops));
                for ($i = 0; $i < count($stops); $i++) {
                    if ($i == 0) {
                        $request_place_params = [
                            'pick_lat' => $request->pick_lat,
                            'pick_lng' => $request->pick_lng,
                            'drop_lat' => $stops[$i]->latitude,
                            'drop_lng' => $stops[$i]->longitude,
                            'pick_address' => $request->pick_address,
                            'drop_address' => $stops[$i]->address,
                            'poly_string' => $request->poly_string,
                            'driver_notes' => $request->driver_notes,
                            'booking_for' => $booking_for,
                            'others_user_id' => $other_user
                                ? $other_user->id
                                : null,
                            'stops' => 1,
                        ];

                        $request_history_params = [
                            'olat' => $request->pick_lat,
                            'olng' => $request->pick_lng,
                            'dlat' => $stops[$i]->latitude,
                            'dlng' => $stops[$i]->longitude,
                            'pick_address' => $request->pick_address,
                            'drop_address' => $stops[$i]->address,
                        ];
                        $request_detail
                            ->requestPlace()
                            ->create($request_place_params);
                        $request_detail
                            ->requestHistory()
                            ->create($request_history_params);
                    } else {
                        // echo  $stops[$i-1]->latitude;
                        $request_place_params = [
                            'pick_lat' => $stops[$i - 1]->latitude,
                            'pick_lng' => $stops[$i - 1]->longitude,
                            'drop_lat' => $stops[$i]->latitude,
                            'drop_lng' => $stops[$i]->longitude,
                            'pick_address' => $stops[$i - 1]->address,
                            'drop_address' => $stops[$i]->address,
                            'poly_string' => $request->poly_string,
                            'booking_for' => $booking_for,
                            'others_user_id' => $other_user
                                ? $other_user->id
                                : null,
                            'stops' => 1,
                        ];

                        $request_history_params = [
                            'olat' => $stops[$i - 1]->latitude,
                            'olng' => $stops[$i - 1]->longitude,
                            'dlat' => $stops[$i]->latitude,
                            'dlng' => $stops[$i]->longitude,
                            'pick_address' => $stops[$i - 1]->address,
                            'drop_address' => $stops[$i]->address,
                        ];
                        $request_detail
                            ->requestPlace()
                            ->create($request_place_params);
                        $request_detail
                            ->requestHistory()
                            ->create($request_history_params);
                    }
                    if ($i == count($stops) - 1) {
                        $request_place_params1 = [
                            'pick_lat' => $stops[$i]->latitude,
                            'pick_lng' => $stops[$i]->longitude,
                            'drop_lat' => $request->drop_lat,
                            'drop_lng' => $request->drop_lng,
                            'pick_address' => $stops[$i]->address,
                            'drop_address' => $request->drop_address,
                            'poly_string' => $request->poly_string,
                            'driver_notes' => $request->driver_notes,
                            'booking_for' => $booking_for,
                            'others_user_id' => $other_user
                                ? $other_user->id
                                : null,
                            'stops' => 1,
                        ];
                        $request_history_params1 = [
                            'olat' => $stops[$i]->latitude,
                            'olng' => $stops[$i]->longitude,
                            'dlat' => $request->drop_lat,
                            'dlng' => $request->drop_lng,
                            'pick_address' => $stops[$i]->address,
                            'drop_address' => $request->drop_address,
                        ];

                        $request_detail
                            ->requestPlace()
                            ->create($request_place_params1);
                        $request_detail
                            ->requestHistory()
                            ->create($request_history_params1);
                    }
                }
            } else {
                //request place detail params
                $request_place_params = [
                    'pick_lat' => $request->pick_lat,
                    'pick_lng' => $request->pick_lng,
                    'drop_lat' => $request->drop_lat,
                    'drop_lng' => $request->drop_lng,
                    'pick_address' => $request->pick_address,
                    'drop_address' => $request->drop_address,
                    'driver_notes' => $request->driver_notes,
                    'others_user_id' => $other_user ? $other_user->id : null,
                    'poly_string' => $request->poly_string,
                ];

                // request history detail params
                $request_history_params = [
                    'olat' => $request->pick_lat,
                    'olng' => $request->pick_lng,
                    'dlat' => $request->drop_lat,
                    'dlng' => $request->drop_lng,
                    'pick_address' => $request->pick_address,
                    'drop_address' => $request->drop_address,
                ];

                $request_detail->requestPlace()->create($request_place_params);
                $request_detail
                    ->requestHistory()
                    ->create($request_history_params);
            }

            $selected_drivers = [];

            $drivers = fetchDrivers(
                $request->pick_lat,
                $request->pick_lng,
                $request->vehicle_type,
                $request->ride_type
            );
            $drivers = json_decode($drivers->getContent());

            // dd($drivers);

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
                    }
                }
            } else {
                return $this->sendError(
                    'No Driver Found',
                    ['request_id' => $request_detail->id, 'error_code' => 2001],
                    404
                );
            }
            if (count($selected_drivers) == 0) {
                return $this->sendError(
                    'No Driver Found',
                    ['request_id' => $request_detail->id, 'error_code' => 2001],
                    404
                );
            }
            // dd($selected_drivers);
            // $metaDriverslug = $selected_drivers[0]['driver_id'];
            // dd($selected_drivers);
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
                    //         $sub_title,
                    //         $pushData,
                    //         $metaDriver->device_info_hash,
                    //         $metaDriver->mobile_application_type,
                    //         1
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

                    $request_meta = $request_detail
                        ->requestMeta()
                        ->create($selected_driver);
                }
            }
            if($request_detail->requestMeta()->count() == 0){
                return $this->sendError(
                    'No Driver Found',
                    ['request_id' => $request_detail->id, 'error_code' => 2001],
                    404
                );
            }
            // dd($metaDriver);
            return $this->sendResponse('Data Found', $result, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error', 'failure.' . $e, 400);
        }
    }

    public function validateUserInTrip($user)
    {
        // dd($user);
        $user_exists_trip = $this->request
            ->where('is_completed', 0)
            ->where('is_cancelled', 0)
            ->where('user_id', $user->id)
            ->where('is_later', 0)
            ->exists();

        if ($user_exists_trip) {
            return $this->sendError('User already in trip', [], 400);
        }
    }

    public function validateUserRequestedTrip($user)
    {
        $request_meta_with_current_user = RequestMeta::where(
            'user_id',
            $user->id
        );

        if ($request_meta_with_current_user->exists()) {
            // get request detail
            $request_with_user = $request_meta_with_current_user
                ->pluck('request_id')
                ->first();
            if ($request_with_user) {
                $this->request
                    ->where('id', $request_with_user)
                    ->update(['is_cancelled' => 1, 'cancel_method' => 1]);
            }
            // Delete all meta details
            $request_meta_with_current_user->delete();
        }
    }

    public function validatePaymentOption($request)
    {
        switch ($request->payment_opt) {
            case 'CARD': // Card payment
                return $this->checkcard($request);
                break;
            case 'CASH': // Cash payment
                return true;
                break;
            case 'WALLET': // Wallet payment
                return $this->checkwallet($request);
                break;
        }
    }

    public function checkCard()
    {
        // @TODO
    }

    /**
     * Check wallet exists or not
     *
     */
    public function checkWallet()
    {
        // @TODO
    }
}
