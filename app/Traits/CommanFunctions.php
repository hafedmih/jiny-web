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
use App\Models\taxi\Promocode;
use App\Models\User;
use App\Models\taxi\Settings;
use App\Models\taxi\Wallet;
use App\Models\taxi\ReferalAmountList;
use App\Models\taxi\WalletTransaction;
use App\Models\boilerplate\Languages;




trait CommanFunctions
{
    private function getZone($lat,$long) {
        $point = new Point($lat, $long);
        // check whether the Zone have the Secondary Zone
        $zone = Zone::contains('map_zone', $point)->where('status', 1)->where('zone_level','PRIMARY')->first();
        if(is_null($zone)){
            //check whether having a Primary Zone
            $zone = Zone::contains('map_zone', $point)->where('status', 1)->where('zone_level','SECONDARY')->first();
        }
        return $zone;
    }

    public function getDistance($pickup_lat,$pickup_long,$drop_lat,$drop_long)
    {
        // $theta = $pickup_long - $drop_long;
        // $dist = sin(deg2rad($pickup_lat)) * sin(deg2rad($drop_lat)) +  cos(deg2rad($pickup_lat)) * cos(deg2rad($drop_lat)) * cos(deg2rad($theta));
        // $dist = acos($dist);
        // $dist = rad2deg($dist);
        // $miles = $dist * 60 * 1.1515;
        // $distance = $miles * 1.609344;
        // return (float)substr($distance,0,4);

        $settings_distance = Settings::where('name','distance_matrix')->first();



        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$pickup_lat.",".$pickup_long."&destinations=".$drop_lat.",".$drop_long."&mode=driving&language=pl-PL&key=".$settings_distance->value."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
  
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

        $aa = explode(" ",$dist);
        if($aa[1] == 'm')
            return 1.0;
        // $url = "https://apis.mapmyindia.com/advancedmaps/v1/eab7d5cae02147918164985738222231/distance_matrix/driving/".$pickup_long.",".$pickup_lat.";".$drop_long.",".$drop_lat."?rtype=1";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $response_a = json_decode($response, true);
    
        // $dist = $response_a['results']['distances'][0][1] / 1000;
        return (float)substr($dist,0,4);



    }
    public function etaCalculation($distance,$base_distance,$base_price,$price_per_distance,$booking_base_fare,$booking_price_km,$outofzonefee)
    {
        $base_amount =  $base_price;
        $distance_amount = 0;
        $booking_km_amount = 0;


        if($distance > $base_distance){
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
            'distance_cost'=>  $booking_km_amount,
            'booking_base_fare'=>  0,
            'booking_km_amount'=>  0,
            'booking_fee'=>  0,
            'outofzonefee'=>  $outofzonefee,
            'sub_total'=>  $sub_total,
        ];
    }

    //public function etaCalculationPromo($expired,$total_amount)
    public function promoCalculation($expired,$total_amount)
    {
        // $total_amount = (double) $total_amount;
        $total_amount = str_replace(',', '', $total_amount);
        if($expired['promo_type'] == 1) {
            $promototal_amount = (double) $total_amount - (double) $expired['amount'];
        }
        else if($expired['promo_type'] == 2){
            $promototal_amount = (double) $expired['percentage'] / 100 * $total_amount;
            $promototal_amount = (double) $total_amount - $promototal_amount;
        } 
        return $promototal_amount > 0 ? number_format($promototal_amount,2) : 0;
    }
   
    public function pushlanguage($lang,$key)
    {
      if($lang=="ta"){
        $languages = Languages::where('status',1)->where('code',$lang)->first(); 
        $language_id = $languages->id; 
        $push_notify =  PushTranslationMaster::where('key_value',$key)->where('language',$language_id)->first();
        return $push_notify;
      }
    }


    public function referalAmountTreansfer($user_id)
    {
        $user = User::where('id',$user_id)->first();
        if(!$user){
            return false;
        }

        if(!$user->user_referral_code){
            return false;
        }
        // dd($user->user_referral_code);
        $receiver = User::where('referral_code',$user->user_referral_code)->first();
        if(!$receiver){
            return false;
        }

        $user_referal_amount = '';
        $user_referal_trip = '';
        if($user->hasRole('user') && $receiver->hasRole('user')){
            $user_referal_amount = Settings::where('name','user_user_referal_amount')->first();
            $user_referal_trip = Settings::where('name','user_user_referal_trip')->first();
        }
        if($user->hasRole('user') && $receiver->hasRole('driver')){
            $user_referal_amount = Settings::where('name','user_driver_referal_amount')->first();
            $user_referal_trip = Settings::where('name','user_driver_referal_trip')->first();
        }
        if($user->hasRole('driver') && $receiver->hasRole('driver')){
            $user_referal_amount = Settings::where('name','driver_driver_referal_amount')->first();
            $user_referal_trip = Settings::where('name','driver_driver_referal_trip')->first();
        }
        if($user->hasRole('driver') && $receiver->hasRole('user')){
            $user_referal_amount = Settings::where('name','driver_user_referal_amount')->first();
            $user_referal_trip = Settings::where('name','driver_user_referal_trip')->first();
        }

        if($user_referal_amount && $user_referal_trip){
            if($user->trips_count >= $user_referal_trip->value){
                $Wallet = Wallet::where('user_id',$receiver->id)->first();
                if(!$Wallet){
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
                    'user_id' => $receiver->id
                ]);

                ReferalAmountList::create([
                    'user_id' => $user->id,
                    'referal_user_id' => $receiver->id,
                    'amount' => $user_referal_amount->value,   
                    'status' => 1
                ]);
                $user->trips_count = 0;
                $user->save();

                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    public function getOauthToken()
    {
        $apiURL = 'https://outpost.mapmyindia.com/api/security/oauth/token';
        $postInput = [
            'grant_type' => 'client_credentials',
            'client_id' => '33OkryzDZsJVKlQoD84iO5CiTKzInTMvfqlbWe6I2-SWStS4d5zYrQkZEelF5bJOJyHgSp7vm2EeZfoVn_u3rw==',
            'client_secret' => 'lrFxI-iSEg-RnEjyKWVWJiSKasNT7RW19GuiyYZPuYakqY_k4dFdz93zgJpIesMmdodjaLYalL7dQHvoeSYSLQVxmHpTmHXe'
        ];
        
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $apiURL, ['form_params' => $postInput, 'headers' => $headers]);
     
        $responseBody = json_decode($response->getBody(), true);

        
        $google_map_token = Settings::where('name','google_map_token')->first();
        // dd($google_map_token);
        if($google_map_token){
            Settings::where('name','google_map_token')->update([
                'value' => $responseBody['access_token']
            ]);
        }
        else{
            Settings::create([
                'name' => 'google_map_token',
                'type' => 'TEXT',
                'value' => $responseBody['access_token']
            ]);
        }
        // config::set('google_map_token',$responseBody['access_token']);
        // dd($google_map_token);
        $setting = Settings::where('status',1)->get();
        // dd($setting);
        $data = [];
        foreach ($setting as $value) {
            $data[$value->name] = $value->image ? $value->image : $value->value;
        }
        session(['data' => $data]);

        return true;
    }

    public function walletTransaction($amount,$user_id,$type,$description,$request_id){
      
        // $type  value must be // SPENT,EARNED
        if($type == 'SPENT'){
            $wallet = Wallet::where('user_id',$user_id)->first();
            if($wallet){
                $wallet->amount_spent = $amount ? $amount : 0;
                $wallet->balance_amount -= $amount ? $amount : 0;
                $wallet->update();
            }
            else{
                $wallet = Wallet::create([
                    'user_id' => $user_id,
                    'amount_spent' => $amount ? $amount : 0,
                    'balance_amount' => $amount ? 0-$amount : 0,
                    'amount_spent' => 0
                ]);
            }
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount ? 0-$amount : 0,
                'purpose' => $description,
                'request_id' => $request_id,
                'type' => $type,
                'user_id' => $user_id
            ]);
        }else if($type == 'EARNED'){
            $wallet = Wallet::where('user_id',$user_id)->first();
            if($wallet){
                $wallet->earned_amount += $amount ? $amount : 0;
                $wallet->balance_amount += $amount ? $amount : 0;
                $wallet->update();
            }
            else{
                $wallet = Wallet::create([
                    'user_id' => $user_id,
                    'earned_amount' => $amount ? $amount : 0,
                    'balance_amount' => $amount ? $amount : 0,
                    'amount_spent' => 0
                ]);
            }
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount ? $amount : 0,
                'purpose' => $description,
                'request_id' => $request_id,
                'type' => $type,
                'user_id' => $user_id
            ]);
        }
        
    }

}