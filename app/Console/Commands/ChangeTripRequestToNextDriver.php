<?php

namespace App\Console\Commands;

use App\Jobs\Request\NoDriverFoundNotifyJob;
use App\Jobs\Request\SendRequestToNextDriverJob;
use App\Models\taxi\Requests\RequestMeta;
use Illuminate\Console\Command;

class ChangeTripRequestToNextDriver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:driver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the request to other drivers when driver doesn\'t respond';

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
        $driver_timeout = 20;

        $request_meta = RequestMeta::whereRaw('TIME_TO_SEC(TIMEDIFF("'.date('Y-m-d H:i:s').'", updated_at)) > '.$driver_timeout." AND active=1")->get();
        
        if (count($request_meta) == 0) {
            return $this->info('no-meta-drivers-found');
        }
        
        $meta_ids = $request_meta->pluck('id');
        $request_ids = $request_meta->pluck('request_id');

        RequestMeta::whereIn('id', $meta_ids)->delete();
        
        $data = RequestMeta::whereIn('request_id', $request_ids)->groupBy('request_id')->selectRaw('Min(id) as request_meta_id, request_id')->get();

        $next_driver_request_meta_id = $data->pluck('request_meta_id');

        $updated_request_id = $data->pluck('request_id');

        $request_meta =  RequestMeta::whereIn('id', $next_driver_request_meta_id)->update(['active'=>true]);
        
        $array_updated_request_ids = $updated_request_id->toArray();
        $array_request_ids = $request_ids->toArray();

        $no_driver_request_ids = array_diff($array_request_ids, $array_updated_request_ids);
        
        // Send Notifications to users - when no drivers found
        dispatch(new NoDriverFoundNotifyJob($no_driver_request_ids));

        // Send Request to other drivers
        dispatch(new SendRequestToNextDriverJob($next_driver_request_meta_id));
    }
}
