<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\taxi\Settings;
use Carbon\Carbon;
use App\Models\taxi\Requests\Request as RequestModel;

use App\Models\User;
use App\Jobs\SendPushNotification;
use App\Constants\PushEnum;
use App\Traits\CommanFunctions;
use App\Models\taxi\PassengerUploadImages;

class NightUploadPhoto extends Command
{
    use CommanFunctions;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'night:uploadphoto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Night Upload Photo User and Driver';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start_time = Settings::where('name','=',"start_night_time")->first();
        $start_time = $start_time ? $start_time->value : "21:00:00";
        $end_time = Settings::where('name','=',"end_night_time")->first();
        $end_time = $end_time ? $end_time->value : "06:00:00";
        $request = RequestModel::where("is_trip_start",1)->where('is_completed',0)->where('is_cancelled',0)->where('skip',0)->get();

        foreach ($request as $key => $value) {
            if(date("H:i:s",strtotime($value->trip_start_time)) >= $start_time || date("H:i:s",strtotime($value->trip_start_time)) <= $end_time){
                $PassengerUploadImages = PassengerUploadImages::where('request_id',$value->id)->where('upload','DRIVER')->first();
                if(!$PassengerUploadImages){
                    $user = User::where('id',$value->driver_id)->role('driver')->where('active',1)->first();
                    if($user){
                        $title = Null;
                        $body = '';
                        $lang = $user->language;
                        $push_data = $this->pushlanguage($lang,'passenger-upload-image');
                        if(is_null($push_data)){
                            $title = 'Upload your image in trip';
                            $body = 'Due to security reasons please upload your image.';
                            $sub_title = 'Due to security reasons please upload your image.';
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title =  $push_data->description;
                        }
                        $pushData = ['notification_enum' => PushEnum::PASSENGER_UPLOAD_IMAGES];
                        dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));
                    }
                }
                $PassengerUploadImages = PassengerUploadImages::where('request_id',$value->id)->where('upload','USER')->first();
                if(!$PassengerUploadImages && $value->is_instant_trip == '0' && $value->if_dispatch == '0'){
                    $user = User::where('id',$value->user_id)->role('user')->where('active',1)->first();
                    if($user){
                        $title = Null;
                        $body = '';
                        $lang = $user->language;
                        $push_data = $this->pushlanguage($lang,'passenger-upload-image');
                        if(is_null($push_data)){
                            $title = 'Passenger upload image in request trip';
                            $body = 'User, Please upload driver image in the trip.';
                            $sub_title = 'User, Please upload driver image in the trip.';
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title =  $push_data->description;
                        }
                        $pushData = ['notification_enum' => PushEnum::PASSENGER_UPLOAD_IMAGES];
                        dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));
                    }
                }
            }
        }
        return 0;
    }
}
