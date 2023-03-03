<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\taxi\DriverLogs;
use Carbon\Carbon;

class ChangeDriverLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:driver_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change the time for driver yestarday date logs';

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
        $drivers = User::role('driver')->where('active',1)->get();

        foreach ($drivers as $key => $value) {
            $driver_log = DriverLogs::where('driver_id',$value->id)->whereNull('offline_time')->first();
            if($driver_log){
                if($driver_log->date < date('Y-m-d')){
                    $driver_log->offline_time = date('Y-m-d 23:59:59', strtotime($driver_log->date));
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $driver_log->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',$driver_log->offline_time);
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $driver_log->working_time = $login_hours;
                    $driver_log->save();

                    $new_driver_log = DriverLogs::where('driver_id',$value->id)->where('date',date('Y-m-d'))->first();
                    if(!$new_driver_log){
                        $new_driver_log = DriverLogs::create([
                            'driver_id' => $value->id,
                            'online_time' => date("Y-m-d 00:00:00"),
                            'date' => date("Y-m-d"),
                            'status' => 1
                        ]);
                    }
                    
                }
            }
        }
        return 0;
    }
}
