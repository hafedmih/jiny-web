<?php

namespace App\Jobs\Request;

use App\Constants\CancelMethod;
use App\Constants\PushEnum;
use App\Jobs\SendPushNotification;
use App\Models\taxi\Requests\Request;
use App\Transformers\Request\TripRequestTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\CommanFunctions;
use Illuminate\Support\Facades\Log;




class NoDriverFoundNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,CommanFunctions;

    protected $requestids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestids)
    {
        $this->requestids = $requestids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->requestids as $key => $request_id) {
            $request_detail = Request::find($request_id);

            $request_detail->update([
                'is_cancelled'=>true,
                'cancel_method'=> CancelMethod::AUTOMATIC,
                'cancelled_at'=>date('Y-m-d H:i:s')
            ]);

            Log::debug("No Driver Found Notify Job");

            $request_detail->fresh();
            $request_result =  fractal($request_detail, new TripRequestTransformer);

            if ($request_detail->userDetail()->exists()) {
                $user = $request_detail->userDetail;

                $title = Null;
                $body = '';
                $lang = $user->language;
                $push_data = $this->pushlanguage($lang,'no-driver');
                if(is_null($push_data)){
                    $title = 'No Driver Found Around You 🙁️';
                    $body = 'Sorry please try again after some times,there is no driver available for your ride now';
                    $sub_title = 'Sorry please try again after some times,there is no driver available for your ride now';

                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                } 

                $pushData = ['notification_enum'=>PushEnum::NO_DRIVER_FOUND,'result'=>(string)$request_result->toJson()];
                dispatch(new SendPushNotification($title,$sub_title, $pushData, $user->token, $user->mobile_application_type,0));
                
                // Form a socket sturcture using users'id and message with event name
                $socket_data = new \stdClass();
                $socket_data->success = true;
                $socket_data->success_message  = PushEnum::NO_DRIVER_FOUND;
                $socket_data->result = $request_result;
                
                $socketData = ['event' => 'request_'.$user->slug,'message' => $socket_data];
                sendSocketData($socketData);
            }
        }
    }
}
