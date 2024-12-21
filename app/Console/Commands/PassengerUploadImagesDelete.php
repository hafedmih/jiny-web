<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\taxi\Settings;
use App\Models\taxi\PassengerUploadImages;
use Carbon\Carbon;

class PassengerUploadImagesDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:uploadimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete for passengers upload request images';

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
        $passenger_upload_images = Settings::where('name',"passenger_upload_images")->first();

        $now = Carbon::now()->subDays($passenger_upload_images ? $passenger_upload_images->value : 2);

        $now = date("Y-m-d H:i:s",strtotime($now));

        $passengerUploadImages = PassengerUploadImages::where('upload_time','<',$now)->where('status',1)->get();

        foreach ($passengerUploadImages as $key => $value) {
            deleteImage('images/passengers',$value->image);

            $value->delete();
        }
        return 0;
    }
}
