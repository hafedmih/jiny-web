<?php
namespace App\Http\Controllers\Taxi\Web\Notification;
use App\Http\Controllers\Controller;
use App\Models\taxi\PushTranslationMaster;
use Illuminate\Http\Request;
use App\Traits\RandomHelper;
use App\Models\boilerplate\Languages;
use App\Http\Requests\Taxi\Web\PushNotificationRequest;
use DB;
use File;
use Validator;


class PushNotificationTranslationController extends Controller
{
    public function list(Request $request)
    {
        $languages = Languages::where('status',1)->get();  
        $pushmasterlist = PushTranslationMaster::get();   
        return view('taxi.notifications.translationMaster',['pushmasterlist' => $pushmasterlist],['languages' => $languages]);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'key_value' => 'required',
            'language' => 'required',
            'title' => 'required',
            'description' => 'required'
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
        $data = $request->all();
        $pushlist =PushTranslationMaster::create([
            'key_value' => $data['key_value'],
            'title' => $data['title'],
            'description' => $data['description'],
            'language' =>$data['language'],
        ]);
        return response()->json(['message' =>'success'], 200);
    }
     public function edit($id)
    {
        
        $push = PushTranslationMaster::where('id',$id)->first();

        return response()->json(['message' =>'success','push' => $push], 200);
        
    }
    public function update(Request $request)
    {
        $data = $request->all();


        $pushlist = PushTranslationMaster::where('key_value',$data['key_value'])->update([
            'key_value' => $data['key_value'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);
        return response()->json(['message' =>'success'], 200);
    }
    public function delete($key)
    {
        $pushMaster = PushTranslationMaster::where('id',$key)->first();
        $pushMaster->delete();
        return redirect()->route('push-transaltion-list');
    }
}
