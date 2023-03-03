<?php

namespace App\Http\Controllers\Taxi\Web\Complaint;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Complaint;
use App\Models\taxi\UserComplaint;
use App\Http\Requests\Taxi\Web\ComplaintSaveRequest;
use App\Models\boilerplate\Languages;
use Carbon\Carbon;
use File;
use DB;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:new-complaints', ['only' => ['complaintsSave']]);
        $this->middleware('permission:edit-complaints', ['only' => ['complaintsEdit','complaintsUpdate']]);
        $this->middleware('permission:delete-complaints', ['only' => ['complaintsDelete']]);
        $this->middleware('permission:status-change-complaints', ['only' => ['complaintsActive']]);
    }
    public function userComplaints(Request $request)
    {
        $user_complaint_normal = UserComplaint::where('category',1)->whereNull('request_id')->get();
        $user_complaint_request = UserComplaint::where('category',1)->whereNotNull('request_id')->get();
        $user_suggession_normal = UserComplaint::where('category',2)->whereNull('request_id')->get();
        $user_suggession_request = UserComplaint::where('category',2)->whereNotNull('request_id')->get();
        
        return view('taxi.complaints.UserComplaints',['user_complaint' => $user_complaint_normal, 'user_complaint_request' => $user_complaint_request,'user_suggession' => $user_suggession_normal,'user_suggession_request' => $user_suggession_request]);
    }

    public function complaints(Request $request)
    {
        // $ComplaintList = Complaint::all();
        $languages = Languages::get();
        $columns = [];
      
       if($languages->count() > 0){
           foreach ($languages as $key => $language){
               $columns[$language->code] = $this->openJSONFile($language->code);
           }
       }
        $ComplaintList = json_decode(json_encode($columns),true);
      //  dd($ComplaintList);
        return view('taxi.complaints.Complaints',['ComplaintList' => $ComplaintList],['languages' => $languages]);
    }

    public function complaintsSave(ComplaintSaveRequest $request)
    {
        $data = $request->all();
        $com = Complaint::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'type' => $data['type'],
            'complaint_type' => $data['complaint_type'], 
            'language' => $data['language'],
            'status' =>$data['status'],

        ]);
       

        // return response()->json(['message' =>'success'], 200);
        $data = $this->openJSONFile($request->language);
        
        // $data[$com->id] = $request->only(['title','type','category','complaint_type','status','language','slug']); 
        $data[$com->slug] = $com; 
        $this->saveJSONFile($request->language, $data);
    return response()->json(['message' =>'success'], 200);

    }

    public function complaintsEdit($key)
    {
        // $complaint = Complaint::where('slug',$id)->first();
        $languages = DB::table('languages')->get();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key])){
                    $complaint=$data[$key];
                }
            }
        }
       // dd($complaint);
        return response()->json(['message' =>'success','complaint' => $complaint,'key'=>$key], 200);
    }

    public function complaintsDelete($key)
    {
        $languages = DB::table('languages')->get();

        if($languages->count() > 0){
             foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                          unset($data[$key]);
                        $this->saveJSONFile($language->code, $data);
             }
           
        }
        return redirect()->route('complaints');
    }

    public function complaintsActive($key)
    {
        $languages = DB::table('languages')->get();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key])){

                   $com = $data[$key];
                    if($com['status'] == "1"){
                        $com['status'] = "0";
                    }
                    else{
                        $com['status'] = "1";
                    }
                    $data[$key]= $com;
                    $this->saveJSONFile($language->code, $data);
                    }
                 
            }
        }
        
        return redirect()->route('complaints');
    }

    public function complaintsUpdate(ComplaintSaveRequest $request)
    {
        $languages = DB::table('languages')->get();

        $com = $request->all();
        if($languages->count() > 0)
        {
            foreach ($languages as $language)
            {
                $data = $this->openJSONFile($language->code);
                if (isset($data[$request->complaint_id]))
                {
                    // $data[$request->complaint_id] = $request->only(['title','type','category','complaint_type','status','language']); 
                
    
                    $c = $data[$request->complaint_id];
                    if($c > 0){
                        $c['title'] = $com['title'];
                        $c['type'] = $com['type'];
                        $c['category'] = $com['category'];
                        $c['complaint_type'] = $com['complaint_type'];
                    }
                    $data[$request->complaint_id]= $c;
                    $this->saveJSONFile($language->code, $data);
                }
            }
        }
        return response()->json(['message' =>'success'], 200);
    }
    private function openJSONFile($code){
        $jsonString = [];
        if(File::exists(base_path('resources/lang/com_'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/com_'.$code.'.json'));
            $jsonString = json_decode($jsonString, true);
        }
        return $jsonString;
    }


    /**
     * Save JSON File
     * @return Response
    */
    private function saveJSONFile($code, $data){
        ksort($data);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path('resources/lang/com_'.$code.'.json'), stripslashes($jsonData));
       
       
    }

}
