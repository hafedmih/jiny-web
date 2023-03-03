<?php

namespace App\Jobs\Request;

use App\Constants\PushEnum;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\RequestMeta;
use App\Transformers\Request\TripRequestTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use app\Traits\CommanFunctions;

class SendRequestToNextDriverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CommanFunctions;

    protected $request_meta_ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request_meta_ids)
    {
        $this->request_meta_ids = $request_meta_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->request_meta_ids as $key => $request_meta_id) {
            $request_meta_detail = RequestMeta::find($request_meta_id);

            $request_result =  fractal($request_meta_detail->request, new TripRequestTransformer);

            if ($request_meta_detail->driver()->exists()) {
                $user = $request_meta_detail->driver;

                $result = fractal($request_meta_detail->request, new TripRequestTransformer);

                $title = Null;
                $body = '';
                $lang = $user->language;
                $push_data = $this->pushlanguage($lang,'trip-created');
                if(is_null($push_data)){
                    $title = 'New Trip Requested 😊️';
                    $body = 'New Trip Requested, you can accept or Reject the request';
                    $sub_title = 'New Trip Requested, you can accept or Reject the request';


                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                } 

               
                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = PushEnum::REQUEST_CREATED;
                $socket_data->result = $result;

                $socketData = ['event' => 'request_'.$user->slug,'message' => $socket_data];
                sendSocketData($socketData);

                $pushData = ['notification_enum' => PushEnum::REQUEST_CREATED];
                dispatch(new SendPushNotification($title,$sub_title, $pushData, $user->token, $user->mobile_application_type,0));
            }
        }
    }
}
