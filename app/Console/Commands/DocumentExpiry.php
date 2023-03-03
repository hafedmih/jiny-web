<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPushNotification;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\Documents;
use App\Models\taxi\Driver;
use App\Models\User;
use App\Models\taxi\Requests\Request;
use Illuminate\Support\Carbon;
use App\Traits\CommanFunctions;
use League\CommonMark\Node\Block\Document;
use App\Constants\PushEnum;

use DateTime;
class DocumentExpiry extends Command
{
    use CommanFunctions;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'before document expiry send push notification to the drivers';

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
        foreach($document as $key => $documentExpiry) {
            $driver_document = DriverDocument::where('status',1)->whereNotNull('expiry_date')->where('exprienc_status',0)->where('document_id','=',$documentExpiry->id)->get();
           //dd($driver_document);
            foreach ($driver_document as $key => $value) {
                if($value->expiry_date != '0000-00-00'){
                    $now = date('Y-m-d');
                    $current_date = new DateTime($now);
                    $exp_date = new DateTime($value->expiry_date);
                        if($current_date < $exp_date){
                            $days = $current_date->diff($exp_date)->format("%a");
                            if($days <= 10){
                                $user = User::where('id',$value->user_id)->where('active',1)->whereNotNull('device_info_hash')->whereNotNull('mobile_application_type')->first();                        
                                if($user){
                                    $title = Null;
                                    $body = '';
                                    $lang = $user->language;
                                    $push_data = $this->pushlanguage($lang,'driver-expiry');
                                    if(is_null($push_data)){
                                    $title = 'Your Document is expired with in '.$days.' days!';
                                    $body = 'Your documents is expired soon';
                                    $sub_title = 'Your documents is expired soon';
                                    
                                    }else{
                                        $title = $push_data->title;
                                        $body =  $push_data->description;
                                        $sub_title = 'Your documents is expired soon';
                                    }
                                    $pushData = ['notification_enum' => PushEnum::DRIVER_EXPIRY];
                                    dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));
                                }
                }        

                    }
                   

                }
                
            }
        }
    }
}