<?php 


    $url = 'https://fcm.googleapis.com/fcm/send';

echo $url;

    $FcmKey = 'AAAAv-qDHPw:APA91bGBDWCiERdaoOPJ5HamZ4PT8jzNd21NGRX-_ie1WC3AtjpdVTbakKdOrMQsLekxG4F49rqQvYhpXQzMmigFBL5obOrNZq2KJeBYWJIUUb5FYbRUG5Ks2wFJK42C2KK6ZjBahGW6';
    
    $image = null;
    if (array_key_exists('image',$this->body)) {
        $image = $this->body['image'];
    }
    $notify_type = 0;
    if($this->notification_type != null){
        $notify_type = 1;
    }
   
    if (strtolower($this->login) == 'android') {
        $data = [
            "registration_ids" => $deviceTokens,
            'data'=>[
                "title" => $this->title,
                'body' => $this->body,
                'image' => $image,
                'notification_type' =>$notify_type, // 1 = General ; 0 = trip
            ],
        ];
    }
    else{
        $data = [
            "registration_ids" => $deviceTokens,
            "notification" => [
                "title" => $this->title,
                "body" => $this->title,  
                "sound" => 1,
                "mutable-content" => 1,
                'image' => $image,
                'notification_type' =>$notify_type, // 1 = General ; 0 = trip
            ],
            'data'=>[
                'body' => $this->body,
            ]
        ];
    }

  


    $RESPONSE = json_encode($data);

    $headers = [
        'Authorization:key=' . $FcmKey,
        'Content-Type: application/json',
    ];

    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $RESPONSE);

    $output = curl_exec($ch);
    if ($output === FALSE) {
        die('Curl error: ' . curl_error($ch));
    }        
    curl_close($ch);
 

?>