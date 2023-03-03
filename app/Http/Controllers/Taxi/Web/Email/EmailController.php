<?php

namespace App\Http\Controllers\Taxi\Web\Email;
// use App\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendPushNotification;
use App\Http\Requests\Taxi\Web\EmailSaveRequest;
use App\Models\User;
use App\Models\taxi\Email;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function email(Request $request)
    {
        $email = Email::orderBy('created_at','DESC')->get();
        $users = User::role('user')->whereNotNull('email')->get();
        // ($users);
        $drivers = User::role('driver')->whereNotNull('email')->get();
        // dd($drivers);
        return view('taxi.email.email',['email' => $email,'users' => $users,'drivers' => $drivers]);
    }
    public function emailSave(EmailSaveRequest $request)
    {
        $data = $request->all();
        $users   = User::whereIn('slug',explode(",",$data['users_id']))->role('user')->pluck('id')->toArray();
        $drivers = User::whereIn('slug',explode(",",$data['driver_id']))->role('driver')->pluck('id')->toArray();

        $user_id = implode(",",$users);
        $driver_id = implode(",",$drivers);

        $email = Email::create([
            'driver_id' => $driver_id,
            'user_id' => $user_id,
            'subject'=>$data['subject'],
            'content'=>$data['content'],
        ]);
        // if($request->has('attachments') && $data['attachments'] != ""){

        //   $filename =  uploadImage('images/email',$request->file('attachments'));
        //   $email->attachments = time() . '.' . $filename;
        //   $email->save();
        // }
        //  dd($email);
        //  $request_data= Email::get();
        foreach($users as $key => $value){
            // dd($users);
            $request_data = Email::where('id',$value)->get();
            // dd($request_data);
            $user = User::where('id',$value)->select('email')->where('email','!=',NULL)->first();
            // \Mail::to($user)->send(new \App\Mail\NewEmail(['subject'=>$data['subject'],'content'=>$data['content']]));
            \Mail::to($user)->send(new \App\Mail\NewEmail($data['subject'],$data['content'],'text/html'));
                // return view('taxi.email.mailtemp',['request_data' => $request_data]);
        }
        foreach($drivers as $key => $value){
            $driver = User::where('id',$value)->select('email')->where('email','!=',NULL)->first();
            \Mail::to($driver)->send(new \App\Mail\NewEmail($data['subject'],$data['content'],'text/html'));
        }
        // Mail::to('jagadeesh.nplus@gmail.com')->send(new \App\Mail\NewEmail($email));
        // return view('emails.RequestBillMailPDF',['settings' => $settings,'request_detail' => $request_detail]);


        return response()->json(['message' =>'success'], 200);
    }
    public function emailDelete($slug)
    {
        $email = Email::where('slug',$slug)->first();
        $email = Email::where('slug',$slug)->delete();
        return redirect()->route('email');
    }
    public function addemail(Request $request)
    {
        // $email = Email::get();
        $users = User::role('user')->get();
        $drivers = User::role('driver')->get();
        return view('taxi.email.addemail',['users' => $users,'drivers' => $drivers]);
    }
}
// Mail::to('karthikbackend.nplus@gmail.com')->send(new \App\Mail\MyTestMail($request_detail,$settings,$pdf));
// return view('emails.RequestBillMailPDF',['settings' => $settings,'request_detail' => $request_detail]);