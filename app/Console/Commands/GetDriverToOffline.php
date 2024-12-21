<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPushNotification;

use App\Models\User;

use App\Models\taxi\Settings;
use App\Models\taxi\Driver;

class GetDriverToOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_driver:offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'driver not upload 30 minits for goto offline';

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
        // $drivers = getNotUpdatedDrivers();

        // $driver_block_rate = Settings::where('name',"driver_block_rate")->first();
        // $driver_block_rate = $driver_block_rate ? $driver_block_rate->value : "0";

        // if($drivers->success){
        //     $drivers = $drivers->data;
        //     foreach ($drivers as $key => $value) {
        //         $user = User::where('id',$value->id)->role('driver')->where('active',1)->first();
        //         if($value->total_rate < $driver_block_rate && $user){
        //             $user->online_by = 0;
        //             $user->save();
        //             dispatch(new SendPushNotification("Driver Your goto Offline",['message' => "Your not updated for time so you goto offline.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
        //         }
        //     }
        // }
        // return 0;
    }
}
