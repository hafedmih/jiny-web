<?php

namespace App\Http\Controllers\Taxi\Web\Office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendPushNotification;
use App\Models\taxi\UserOtp;

class OfficeController extends Controller
{
    public function otp(Request $request)
    {
        $otp = UserOtp::orderBy('created_at','DESC')->get();
        
        return view('taxi.office.office',['otp' => $otp]);
    }
}
