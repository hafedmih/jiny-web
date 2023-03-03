<?php

namespace App\Http\Controllers\Taxi\Web\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendPushNotification;
use App\Http\Requests\Taxi\Web\NotificationSaveRequest;
use App\Models\User;
use App\Models\taxi\Notification;

class NotificationController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:new-notification', ['only' => ['notificationSave']]);
        $this->middleware('permission:delete-notification', ['only' => ['notificationDelete']]);
    }

    public function notification(Request $request)
    {
        $notification = Notification::where('notification_type','GENERAL')->orderBy('created_at','DESC')->get();
        $users = User::role('user')->get();
        $drivers = User::role('driver')->get();

        return view('taxi.notifications.Notifications',['notification' => $notification,'users' => $users,'drivers' => $drivers]);
    }

    public function notificationAdd(Request $request)
    {
        $notification = Notification::where('notification_type','GENERAL')->orderBy('created_at','DESC')->get();
        $users = User::role('user')->get();
        $drivers = User::role('driver')->get();

        return view('taxi.notifications.AddNotification',['notification' => $notification,'users' => $users,'drivers' => $drivers]);
    }

    public function notificationSave(NotificationSaveRequest $request)
    {
        $data = $request->all();

        if(!$data['users_id'] && !$data['driver_id'])
            return response()->json(['message' =>'Users or Drivers is required...'], 200);
            
        $users   = User::whereIn('slug',explode(",",$data['users_id']))->role('user')->pluck('id')->toArray();
        $drivers = User::whereIn('slug',explode(",",$data['driver_id']))->role('driver')->pluck('id')->toArray();

       //dd($drivers);
        
        $user_id = implode(",",$users);
        $driver_id = implode(",",$drivers);

        // $filename =  uploadImage('images/notification',$request->file('image1'));
        if($request->file('image1')){
            $filename =  uploadImage('images/notification',$request->file('image1'));
        }
        else{
            $filename = "";
        }
        if($request->file('image2')){
            $filename2 =  uploadImage('images/notification',$request->file('image2'));
        }
        else{
            $filename2 = "";
        }
        if($request->file('image3')){
            $filename3 =  uploadImage('images/notification',$request->file('image3'));
        }
        else{
            $filename3 = "";
        }

        $insert = Notification::create([
            'title' => $data['title'],
            'driver_id' => $driver_id,
            'user_id' => $user_id,
            'sub_title' => $data['sub_title'],
            'message' => $data['message'],
            'has_redirect_url' => $data['has_redirect_url'],
            'redirect_url' => $data['redirect_url'],
            'image1' => $filename,
            'image2' => $filename2,
            'image3' => $filename3,
            'date' => NOW(),
            'notification_type' => 'GENERAL',
            'status' => 1
        ]);

        foreach ($users as $key => $value) {
           $user = User::where('id',$value)->first();
           dispatch(new SendPushNotification($data['title'],$data['sub_title'],['message' => $data['message'],'image' => $insert->images1],$user->device_info_hash,$user->mobile_application_type,0));
        }
        foreach ($drivers as $key => $value) {
            $driver = User::where('id',$value)->first();
             if($driver->device_info_hash != Null && $driver->device_info_hash != 'ABCD' && $driver->device_info_hash != 'abcd')
             {
                dispatch(new SendPushNotification($data['title'],$data['sub_title'],['message' => $data['message'],'image' => $insert->images1],$driver->device_info_hash,$driver->mobile_application_type,0));
             }
        }

        return response()->json(['message' =>'success'], 200);

    }

    public function notificationDelete($id)
    {
        $notification = Notification::where('slug',$id)->first();
        deleteImage('public/images/notification',$notification->image1);
        deleteImage('public/images/notification',$notification->image2);
        deleteImage('public/images/notification',$notification->image3);
        $notification = Notification::where('slug',$id)->delete();
        return redirect()->route('notification');
    }
}
