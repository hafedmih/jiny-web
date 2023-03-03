<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait RandomHelper
{
    private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function RandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $String = '';
        for ($i = 0; $i < $length; $i++) {
            $String .= $characters[rand(0, $charactersLength - 1)];
        }
        return $String;
    }


    public function generateRandomnumber()
    {
        $response = $this->UniqueRandomNumbersWithinRange(100, 999, 25);
        return response()->json(['response'=>$response], 200);
    } 
    public function getShaValue(Request $request)
    {
        $array = trim($request->input('name'));
        // dd($array);
        $array = explode(",",$array);
        $response = array_chunk($array,3);
        $orginalshavalue='';
        for($i=0;$i<count($response[1]);$i++)
        {
            $orginalshavalue = $orginalshavalue.hash('sha256', $response[1][$i]);
        }
        $shavalue = hash('sha256', $orginalshavalue);
        return response()->json(['response'=>$shavalue], 200);
    }
    public function saveShaValue(Request $request)
    {
        $orginalshavalue = trim($request->input('name'));
        $shavalue = hash('sha256', $orginalshavalue);
        $client = $this::getCurrentClient(request());
        $user = User::find($client->user_id);

        return response()->json(['user'=>$user,"value"=>$shavalue], 200);
    }
    // Generate Random Salt
    public function UniqueRandomNumbers($quantity) 
    {

        $randomNumber = random_int(1000, 9999);
        return $randomNumber;


        // $count=0;

        // $random_number='';
        // while ( $count < $quantity ) {
        //     $random_digit = rand(0, 9);
        //     $random_number .= $random_digit;
        //     $count++;
        // }
        // if(strlen($random_number) == 3){

        //     $random_number .= 0;
        //     return $random_number;
        // }else{     
        //     return $random_number;
        // }
       
    } 
    

    public function UniqueRandomNumbersWithinRangeArray($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }
    private function UniqueRandomNumbersWithinRange($quantity) {
        
        $count=0;

        $random_number='';
        while ( $count < $quantity ) {
            $random_digit = mt_rand(0, 9);
            $random_number .= $random_digit;
            $count++;
        }
        return $random_number;
    }    
    function encrypt($data) {
        $key = "yejqewj1q3gjt86c039e";
        $plaintext = $data;
        $ivlen = openssl_cipher_iv_length($cipher = encryption_method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }
    


}