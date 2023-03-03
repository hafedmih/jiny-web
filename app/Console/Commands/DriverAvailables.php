<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\taxi\Requests\Request;
use App\Models\taxi\ZonePrice;

class DriverAvailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'available:set_drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set availables drivers';

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

        $request = Request::where('is_later',1)->where('is_driver_started',0)->where('is_cancelled',0)->where('hold_status',1)->get();

        foreach ($request as $key => $value) {
            $zone_type = ZonePrice::where('id',$value->zone_type_id)->first();
            $drivers = fetchDrivers($value->requestPlace->pick_lat,$value->requestPlace->pick_lng,$zone_type->type_id, $value->ride_type);
            $drivers = json_decode($drivers->getContent());

            if ($drivers->success == true) {
                $value->availables_status = 1;
            }
            else{
                $value->availables_status = 0;
            }
            $value->save();
        }
        return 0;
    }
}
