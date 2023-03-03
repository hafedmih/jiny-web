<?php

namespace App\Http\Controllers\Taxi\Web\Sms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\SmsSaveRequest;
use App\Models\User;
use App\Models\taxi\Sms;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{

    public function sms(Request $request)
    {
        $smsmanage = Sms::orderBy('created_at','DESC')->get();
        $users = User::role('user')->get();
        $drivers = User::role('driver')->get();

        return view('taxi.sms.sms',['smsmanage' => $smsmanage,'users' => $users,'drivers' => $drivers]);
    }
    public function smsSave(SmsSaveRequest $request)
    {
        $data = $request->all();

        if(!$data['users_id'] && !$data['driver_id'])
            return response()->json(['message' =>'Users or Drivers is required...'], 200);
            
        $users   = User::whereIn('slug',explode(",",$data['users_id']))->role('user')->pluck('id')->toArray();
        $drivers = User::whereIn('slug',explode(",",$data['driver_id']))->role('driver')->pluck('id')->toArray();
        
        $user_id = implode(",",$users);
        $driver_id = implode(",",$drivers);
// dd($users);
        $insert = Sms::create([
            'title' => $data['title'],
            'driver_id' => $driver_id,
            'user_id' => $user_id,
            'message' => $data['message'],
            'date' => NOW(),
        ]);
        // foreach($users as $key => $value)
        //     {    
        //         $user = User::where('id',$value)->first();
        //         $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$user->phone_number.$data['title'].'&message=Hi'.$user->firstname.$data['message'].'.Thank you for using our taxi service . Our City Our Taxi !!! - NPTECH.');
        //     }
        // foreach($drivers as $key => $value)
        //     {
        //         $driver = User::where('id',$value)->first();
        //         $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$driver->phone_number.$data['title'].'&message=Hi '.$driver->firstname.$data['message'].' . Thank you for using our taxi service . Our City Our Taxi !!! - NPTECH.');
        //     }        

        return response()->json(['message' =>'success'], 200);
    }
    public function smsDelete($slug)
    {
        $Sms = Sms::where('slug',$slug)->first();
        $Sms = Sms::where('slug',$slug)->delete();
        return redirect()->route('sms');
    }
}