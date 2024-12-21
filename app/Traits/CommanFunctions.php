<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Models\taxi\PushTranslationMaster;
use App\Models\taxi\Zone;
use App\Models\taxi\RiderAddress;
use App\Models\taxi\Promocode;
use App\Models\User;
use App\Models\taxi\Settings;
use App\Models\taxi\Wallet;
use App\Models\taxi\ReferalAmountList;
use App\Models\taxi\WalletTransaction;
use App\Models\boilerplate\Languages;
use Illuminate\Support\Facades\Http;

trait CommanFunctions
{
    private function getZone($lat, $long)
    {
        $point = new Point($lat, $long);
        // check whether the Zone have the Secondary Zone
        $zone = Zone::contains('map_zone', $point)
            ->where('status', 1)
            ->where('zone_level', 'PRIMARY')
            ->first();
        if (is_null($zone)) {
            //check whether having a Primary Zone
            $zone = Zone::contains('map_zone', $point)
                ->where('status', 1)
                ->where('zone_level', 'SECONDARY')
                ->first();
        }
        return $zone;
    }

    private function getRider($lat, $long)
    {
        $point = new Point($lat, $long);
        // check whether the Zone have the Secondary Zone
        $zone = RiderAddress::contains('location', $point)->first();
        if (is_null($zone)) {
            //check whether having a Primary Zone
            $zone = RiderAddress::contains('location', $point)->first();
        }
        return $zone;
    }
    
      public function reverse($latitude)
    {
    
        //dd($latitude);
        $settings_geocode = Settings::where('name', 'geo_coder')->first();
        $apiKey = $settings_geocode->value;

        $client = new \GuzzleHttp\Client();
        
        $response = $client->get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'query' => [
                    'latlng' => $latitude,
                    'key' => $apiKey,
                ],
            ]
        );
        
        

        $data = json_decode($response->getBody(), true);
        if ($data['status'] === 'OK' && isset($data['results'][0])) {
            $result = $data['results'][0];
            $address = $result['formatted_address'];

            // Use the address as needed
            return $address;
        } else {
            // Handle reverse geocoding errors
            return response()->json(['error' => 'Reverse geocoding failed']);
        }
    }
    

    public function getDistance(
        $pickup_lat,
        $pickup_long,
        $drop_lat,
        $drop_long
    ) {

        $apiURL1 = 'http://81.169.238.2:8080/ors/v2/matrix/driving-car';
        $apiURL2 = 'http://81.169.238.2:8080/ors/v2/matrix/cycling-road';
        $headers = array();
        $headers['Accept'] = 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8';
        $headers['Content-Type'] = 'application/json;charset=UTF-8';
        $locationObject = array();
        // $locationObject['locations'] = array(['-15.971150062687231','18.102311204601136'],['-15.975784919864967','18.098884712664734']);
        $locationObject['locations'] = array([$pickup_long,$pickup_lat],[$drop_long,$drop_lat]);
        $locationObject['metrics'] = ["distance"];

        $response = Http::withHeaders($headers)->post($apiURL1, $locationObject);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        $value1 = isset($responseBody['distances']) && isset($responseBody['distances'][0]) && isset($responseBody['distances'][0][1]) ? (float) $responseBody['distances'][0][1] / 1000 : 0;

        $response = Http::withHeaders($headers)->post($apiURL2, $locationObject);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        $value2 = isset($responseBody['distances']) && isset($responseBody['distances'][0]) && isset($responseBody['distances'][0][1]) ? (float) $responseBody['distances'][0][1] / 1000 : 0;

        return $value2 > $value1 ? $value1 : $value2;

        // $settings_distance = Settings::where(
        //     'name',
        //     'distance_matrix'
        // )->first();

        // $url =
        //     'https://maps.googleapis.com/maps/api/directions/json?origin=' .
        //     $pickup_lat .
        //     ',' .
        //     $pickup_long .
        //     '&destination=' .
        //     $drop_lat .
        //     ',' .
        //     $drop_long .
        //     '&mode=driving&language=pl-PL&key=' .
        //     $settings_distance->value .
        //     '&alternatives=true';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $response_a = json_decode($response, true);
        // $shortestdistance = 0;
        // if (count($response_a['routes']) != 1) {
        //     foreach ($response_a['routes'] as $routes) {
        //         $distwithkm = $routes['legs'][0]['distance']['text'];
        //         $aa = explode(' ', $distwithkm);
        //         if ($aa[1] == 'm') {
        //             $newdist = 0;
        //         } else {
        //             $newdist = (float) substr($distwithkm, 0, 4);
        //         }
        //         $shortestdistance = $newdist;
        //         if ($shortestdistance > $newdist) {
        //             $shortestdistance = $newdist;
        //         }
        //     }
        //     return $shortestdistance;
        // } else {
        //     $distwithkm =
        //         $response_a['routes'][0]['legs'][0]['distance']['text'];
        //     $aa = explode(' ', $distwithkm);
        //     if ($aa[1] == 'm') {
        //         $newdist = 0;
        //     } else {
        //         $newdist = (float) substr($distwithkm, 0, 4);
        //     }
        //     return $newdist;
        // }
    }
    public function etaCalculation(
        $distance,
        $base_distance,
        $base_price,
        $price_per_distance,
        $booking_base_fare,
        $booking_price_km,
        $outofzonefee
    ) {
        $base_amount = $base_price;
        $distance_amount = 0;
        $booking_km_amount = 0;

        if ($distance > $base_distance) {
            $balance_distance = $distance - $base_distance;
            $distance_amount = $balance_distance * $price_per_distance;
        }
        // Booking fee calculation
        // if($booking_base_fare != 0){
        //     if($distance > $base_distance){
        //         $balance_distance = $distance - $base_distance;
        //         $booking_km_amount = $balance_distance * $booking_price_km;
        //     }
        // }

        $sub_total = $base_amount + $distance_amount + $outofzonefee;

        return $data = [
            'base_amount' => $base_amount,
            'distance_cost' => $booking_km_amount,
            'booking_base_fare' => 0,
            'booking_km_amount' => 0,
            'booking_fee' => 0,
            'outofzonefee' => $outofzonefee,
            'sub_total' => $sub_total,
        ];
    }

    //public function etaCalculationPromo($expired,$total_amount)
    public function promoCalculation($expired, $total_amount)
    {
        // $total_amount = (double) $total_amount;
        $total_amount = str_replace(',', '', $total_amount);
        if ($expired['promo_type'] == 1) {
            $promototal_amount =
                (float) $total_amount - (float) $expired['amount'];
        } elseif ($expired['promo_type'] == 2) {
            $promototal_amount =
                ((float) $expired['percentage'] / 100) * $total_amount;
            $promototal_amount = (float) $total_amount - $promototal_amount;
        }
        return $promototal_amount > 0
            ? number_format($promototal_amount, 2)
            : 0;
    }

    public function pushlanguage($lang, $key)
    {
        if ($lang == 'ta') {
            $languages = Languages::where('status', 1)
                ->where('code', $lang)
                ->first();
            $language_id = $languages->id;
            $push_notify = PushTranslationMaster::where('key_value', $key)
                ->where('language', $language_id)
                ->first();
            return $push_notify;
        }
    }

    public function referalAmountTreansfer($user_id)
    {
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return false;
        }

        if (!$user->user_referral_code) {
            return false;
        }
        // dd($user->user_referral_code);
        $receiver = User::where(
            'referral_code',
            $user->user_referral_code
        )->first();
        if (!$receiver) {
            return false;
        }

        $user_referal_amount = '';
        $user_referal_trip = '';
        if ($user->hasRole('user') && $receiver->hasRole('user')) {
            $user_referal_amount = Settings::where(
                'name',
                'user_user_referal_amount'
            )->first();
            $user_referal_trip = Settings::where(
                'name',
                'user_user_referal_trip'
            )->first();
        }
        if ($user->hasRole('user') && $receiver->hasRole('driver')) {
            $user_referal_amount = Settings::where(
                'name',
                'user_driver_referal_amount'
            )->first();
            $user_referal_trip = Settings::where(
                'name',
                'user_driver_referal_trip'
            )->first();
        }
        if ($user->hasRole('driver') && $receiver->hasRole('driver')) {
            $user_referal_amount = Settings::where(
                'name',
                'driver_driver_referal_amount'
            )->first();
            $user_referal_trip = Settings::where(
                'name',
                'driver_driver_referal_trip'
            )->first();
        }
        if ($user->hasRole('driver') && $receiver->hasRole('user')) {
            $user_referal_amount = Settings::where(
                'name',
                'driver_user_referal_amount'
            )->first();
            $user_referal_trip = Settings::where(
                'name',
                'driver_user_referal_trip'
            )->first();
        }

        if ($user_referal_amount && $user_referal_trip) {
            if ($user->trips_count >= $user_referal_trip->value) {
                $Wallet = Wallet::where('user_id', $receiver->id)->first();
                if (!$Wallet) {
                    $Wallet = new Wallet();
                    $Wallet->user_id = $receiver->id;
                }
                $Wallet->earned_amount += $user_referal_amount->value;
                $Wallet->balance_amount += $user_referal_amount->value;
                $Wallet->save();

                WalletTransaction::create([
                    'wallet_id' => $Wallet->id,
                    'amount' => $user_referal_amount->value,
                    'purpose' => 'wallet amount added successfully',
                    'type' => 'EARNED',
                    'user_id' => $receiver->id,
                ]);

                ReferalAmountList::create([
                    'user_id' => $user->id,
                    'referal_user_id' => $receiver->id,
                    'amount' => $user_referal_amount->value,
                    'status' => 1,
                ]);
                $user->trips_count = 0;
                $user->save();

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getOauthToken()
    {
        $apiURL = 'https://outpost.mapmyindia.com/api/security/oauth/token';
        $postInput = [
            'grant_type' => 'client_credentials',
            'client_id' =>
                '33OkryzDZsJVKlQoD84iO5CiTKzInTMvfqlbWe6I2-SWStS4d5zYrQkZEelF5bJOJyHgSp7vm2EeZfoVn_u3rw==',
            'client_secret' =>
                'lrFxI-iSEg-RnEjyKWVWJiSKasNT7RW19GuiyYZPuYakqY_k4dFdz93zgJpIesMmdodjaLYalL7dQHvoeSYSLQVxmHpTmHXe',
        ];

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $apiURL, [
            'form_params' => $postInput,
            'headers' => $headers,
        ]);

        $responseBody = json_decode($response->getBody(), true);

        $google_map_token = Settings::where(
            'name',
            'google_map_token'
        )->first();
        // dd($google_map_token);
        if ($google_map_token) {
            Settings::where('name', 'google_map_token')->update([
                'value' => $responseBody['access_token'],
            ]);
        } else {
            Settings::create([
                'name' => 'google_map_token',
                'type' => 'TEXT',
                'value' => $responseBody['access_token'],
            ]);
        }
        // config::set('google_map_token',$responseBody['access_token']);
        // dd($google_map_token);
        $setting = Settings::where('status', 1)->get();
        // dd($setting);
        $data = [];
        foreach ($setting as $value) {
            $data[$value->name] = $value->image ? $value->image : $value->value;
        }
        session(['data' => $data]);

        return true;
    }

    public function walletTransaction(
        $amount,
        $user_id,
        $type,
        $description,
        $request_id
    ) {
        // $type  value must be // SPENT,EARNED
        if ($type == 'SPENT') {
            $wallet = Wallet::where('user_id', $user_id)->first();
            if ($wallet) {
                $wallet->amount_spent = $amount ? $amount : 0;
                $wallet->balance_amount -= $amount ? $amount : 0;
                $wallet->update();
            } else {
                $wallet = Wallet::create([
                    'user_id' => $user_id,
                    'amount_spent' => $amount ? $amount : 0,
                    'balance_amount' => $amount ? 0 - $amount : 0,
                    'amount_spent' => 0,
                ]);
            }
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount ? 0 - $amount : 0,
                'purpose' => $description,
                'request_id' => $request_id,
                'type' => $type,
                'user_id' => $user_id,
            ]);
        } elseif ($type == 'EARNED') {
            $wallet = Wallet::where('user_id', $user_id)->first();
            if ($wallet) {
                $wallet->earned_amount += $amount ? $amount : 0;
                $wallet->balance_amount += $amount ? $amount : 0;
                $wallet->update();
            } else {
                $wallet = Wallet::create([
                    'user_id' => $user_id,
                    'earned_amount' => $amount ? $amount : 0,
                    'balance_amount' => $amount ? $amount : 0,
                    'amount_spent' => 0,
                ]);
            }
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount ? $amount : 0,
                'purpose' => $description,
                'request_id' => $request_id,
                'type' => $type,
                'user_id' => $user_id,
            ]);
        }
    }

    public function transactionCheck($token, $data)
    {
        $url = 'https://ebankily-tst.appspot.com/checkTransaction';
        $authorization = 'Authorization: Bearer ' . $token;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url, // your preferred url
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                'accept: */*',
                'accept-language: en-US,en;q=0.8',
                'content-type: application/json',
                $authorization,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // $response = json_decode($get_response, 1);
        return $response;
    }

    public function refreshToken($refresh_token_data)
    {
        //  dd($refresh_token_data);

        // dd($refresh_token_data);
        $url = 'https://ebankily-tst.appspot.com/authentification';
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url, // your preferred url
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($refresh_token_data),
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                'accept: */*',
                'accept-language: en-US,en;q=0.8',
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // $response = json_decode($get_response, 1);
        dd($response);
        return $response;
    }
}
