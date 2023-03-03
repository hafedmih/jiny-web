<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPushNotification;

use App\Models\User;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Settings;
use App\Models\taxi\Wallet;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\Driver;
use App\Traits\CommanFunctions;
use App\Constants\PushEnum;

use DB;

class LowRatingBlockDriver extends Command
{
    use CommanFunctions;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lowrate:blocked_driver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Driver blocked for low rating flow';

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
        // Driver blocked for low rating flow
        // $driver_block_rate = Settings::where('name',"driver_block_rate")->first();
        // $driver_block_rate = $driver_block_rate ? $driver_block_rate->value : "0";

        // $drivers = User::role('driver')->where('active',1)->get();

        // foreach ($drivers as $key => $value) {
        //     $user = User::where('id',$value->id)->role('driver')->where('active',1)->first();
        //     if($value->rating < $driver_block_rate){
        //         $user->active = 0;
        //         $user->block_reson = "Driver Low Ratings";
        //         $user->save();

        //         $title = Null;
        //         $body = '';
        //         $lang = $user->language;
        //         $push_data = $this->pushlanguage($lang,'driver-blocked');
        //         if(is_null($push_data)){
        //            $title = 'Driver Your Account Is Blocked';
        //            $body = 'Your request rating is low. So, your account is blocked. Please, contact admin.';
        //            $sub_title = 'Your request rating is low. So, your account is blocked. Please, contact admin.';

        //         }else{
        //             $title = $push_data->title;
        //             $body =  $push_data->description;
        //             $sub_title =  $push_data->description;

        //         }

        //         $pushData = ['notification_enum' => PushEnum::DRIVER_BLOCKED];

        //         dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,1));

        //         // dispatch(new SendPushNotification("Driver Your Account Is Blocked",['message' => "Your request rating is low. So, your account is blocked. Please, contact admin.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
        //     //}
        //     }
        // }


        

        // Driver blocked for low wallet ballence flow
        $driver_block_wallet_balance = Settings::where('name',"driver_block_wallet_balance")->first();
        $driver_block_wallet_balance = $driver_block_wallet_balance ? $driver_block_wallet_balance->value : '0';

        $driver_wallet_balance = Wallet::get();

        foreach ($driver_wallet_balance as $key => $value) {
            $user = User::where('id',$value->user_id)->role('driver')->where('active',1)->first();
            if($value->balance_amount < $driver_block_wallet_balance && $user){
                $user->active = 0;
                $user->block_reson = "Driver Low Wallet Balance";
                $user->save();

                $title = Null;
                $body = '';
                $lang = $user->language;
                $push_data = $this->pushlanguage($lang,'driver-blocked');
                if(is_null($push_data)){
                   $title = 'Driver Your Account Is Blocked';
                   $body = 'Your wallet balance is very low. So, your account is blocked. Please, recharge immediately.';
                   $sub_title = 'Your wallet balance is very low. So, your account is blocked. Please, recharge immediately.';

                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                }

                $pushData = ['notification_enum' => PushEnum::DRIVER_BLOCKED];

                dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));

                // dispatch(new SendPushNotification("Driver Your Account Is Blocked",['message' => "Your wallet balance is very low. So, your account is blocked. Please, recharge immediately.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
            }
        }

        // Driver blocked for low acceptance ratio flow
        // $driver_block_acceptance_ratio = Settings::where('name',"driver_block_acceptance_ratio")->first();
        // $driver_block_acceptance_ratio = $driver_block_acceptance_ratio ? $driver_block_acceptance_ratio->value : "0";

        // $driver_acceptence = Driver::where('status',1)->get();
        // foreach ($driver_acceptence as $key => $value) {
        //     $user = User::where('id',$value->user_id)->role('driver')->where('active',1)->first();
        //     if($value->acceptance_ratio < $driver_block_acceptance_ratio && $user){
        //         $user->active = 0;
        //         $user->block_reson = "Driver Low Acceptance Ratio";
        //         $user->save();
        //         $title = Null;
        //         $body = '';
        //         $lang = $user->language;
        //         $push_data = $this->pushlanguage($lang,'driver-blocked');
        //         if(is_null($push_data)){
        //            $title = 'Driver Your Account Is Blocked';
        //            $body = 'Your acceptance ratio is very low. So, your account is blocked. Please, contact admin.';
        //         }else{
        //             $title = $push_data->title;
        //             $body =  $push_data->description;
        //         }

        //         $pushData = ['notification_enum' => PushEnum::DRIVER_BLOCKED];

        //         dispatch(new SendPushNotification($title,$pushData,$user->device_info_hash,$user->mobile_application_type,1));

        //         // dispatch(new SendPushNotification("Driver Your Account Is Blocked",['message' => "Your acceptance ratio is very low. So, your account is blocked. Please, contact admin.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
        //     }
        // }

        // Driver type subscription and no subscriped for blocked flow
        // $drivers = Driver::where('subscription_type','SUBSCRIPTION')->get();

        // foreach ($drivers as $key => $value) {
        //     $old_subscription = DriverSubscriptions::where('user_id',$value->user_id)->where('to_date','>=',NOW())->count();
        //     if($old_subscription == 0){
        //         $user = User::where('id',$value->user_id)->role('driver')->where('active',1)->first();
        //         $user->active = 0;
        //         $user->block_reson = "Driver didn't subscribed any plan";
        //         $user->save();
        //         $title = Null;
        //         $body = '';
        //         $lang = $user->language;
        //         $push_data = $this->pushlanguage($lang,'driver-blocked');
        //         if(is_null($push_data)){
        //            $title = 'You are not subscribed any plan';
        //            $body = 'You are not subscribed any plan. So, your account is blocked. Please, Subscripe.';
        //            $sub_title = 'You are not subscribed any plan. So, your account is blocked. Please, Subscripe.';

        //         }else{
        //             $title = $push_data->title;
        //             $body =  $push_data->description;
        //             $sub_title =  $push_data->description;

        //         }
        //         dispatch(new SendPushNotification("You are not subscribed any plan","You are not subscribed any plan. So, your account is blocked. Please, Subscribe.",['message' => "You are not subscribed any plan. So, your account is blocked. Please, Subscripe.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
        //     }

        // }

        // return 0;
    }
}
