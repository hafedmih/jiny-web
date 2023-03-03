<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPushNotification;

use App\Models\taxi\DriverDocument;
use App\Models\taxi\Documents;
use App\Models\taxi\Driver;
use App\Models\User;
use App\Models\taxi\Requests\RequestHistory;
use App\Models\taxi\Requests\Request;
use Illuminate\Support\Carbon;
use App\Traits\CommanFunctions;
use League\CommonMark\Node\Block\Document;
use App\Constants\PushEnum;


class expriedUserBlock extends Command
{
    use CommanFunctions;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'driver:block';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'blocked drivers for document expried date completed';

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
        $document = Documents::where('status',1)->where('requried',1)->where('expiry_date',1)->get();
        foreach ($document as $key => $test) 
        {
        $driver_document = DriverDocument::where('status',1)->whereNotNull('expiry_date')->where('document_id','=',$test->id)->get();
      
        foreach ($driver_document as $key => $value) {
            if($value->expiry_date != '0000-00-00'){
                if($value->expiry_date < date('Y-m-d')){
                    $value->exprienc_status = 1;
                    $value->exprience_reson = "Driver Documents Expired";
                    $value->save();


                    $user = User::where('id',$value->user_id)->where('active',1)->whereNotNull('device_info_hash')->whereNotNull('mobile_application_type')->first();
                    
                    if($user){
                        User::where('id',$value->user_id)->where('active',1)->whereNotNull('device_info_hash')->whereNotNull('mobile_application_type')->update(['active' => 0,'block_reson' => "Driver Documents Expired"]);
                        Driver::where('user_id',$value->user_id)->update(['document_upload_status' => 5]);

                        $title = Null;
                        $body = '';
                        $lang = $user->language;
                        $push_data = $this->pushlanguage($lang,'driver-blocked');
                        if(is_null($push_data)){
                           $title = 'Driver Your Account Is Blocked';
                           $body = 'Your documents expired. So, your account is blocked.';
                           $sub_title = 'Your documents expired. So, your account is blocked.';
                           
                        }else{
                            $title = $push_data->title;
                            $body =  $push_data->description;
                            $sub_title = 'Your documents expired. So, your account is blocked.';
                        }
                        $pushData = ['notification_enum' => PushEnum::DRIVER_BLOCKED];

                        dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));

                        // dispatch(new SendPushNotification("Driver Your Account Is Blocked","Your documents expired. So, your account is blocked.",$pushData,['message' => "Your documents expired. So, your account is blocked. Please, upload new documents.",'image' => ''],$user->device_info_hash,$user->mobile_application_type,1));
                    }
                }
            }
        }

    }

        $request_history = RequestHistory::whereNull('olat')->whereNull('olng')->get();

        foreach ($request_history as $key => $value) {
            $request_time = Carbon::parse($value->created_at)->addMinutes(40);

            if(Carbon::now() > $request_time){
                Request::where('id',$value->request_id)->update(['location_approve' => 0]);

                $value->delete();
            }
        }
    }
}
