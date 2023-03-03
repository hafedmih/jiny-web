<?php

namespace App\Http\Controllers\Taxi\Web\Complaint;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Complaint;
use App\Models\taxi\UserComplaint;
use App\Models\taxi\Tripcomplaint;
use App\Http\Requests\Taxi\Web\ComplaintSaveRequest;
use App\Http\Requests\Taxi\Web\TripComplaintSaveRequest;
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
        $languages = Languages::where('status',1)->get();
        
        $ComplaintList   = Complaint::get();
      //  dd($ComplaintList);
      
        return view('taxi.complaints.Complaints',['ComplaintList' => $ComplaintList],['languages' => $languages]);
    }

    public function complaintsSave(ComplaintSaveRequest $request)
    {
        $data = $request->all(); 
        $com = Complaint::create([
            'title' => $data['title'], 
            'type' => $data['type'],
            'language' => $data['language'],
            'status' =>$data['status'],
            'category' => $data['category'],

        ]);
        return response()->json(['message' =>'success'], 200);
       
    }

    public function complaintsEdit($slug)
    {
        $complaint = Complaint::where('slug',$slug)->first();
               
        return response()->json(['message' =>'success','complaint' => $complaint], 200);
    }

    public function complaintsDelete($slug)
    {
        $complaint = Complaint::where('slug',$slug)->first();
        $complaints = UserComplaint::where('complaint_id',$complaint->id)->count();
        if($complaints > 0){
            session()->flash('message',"Cannot delete this Complaint");
            return back();
        }
        $complaint = Complaint::where('slug',$slug)->delete();
        
        return redirect()->route('complaints');
    }

    public function complaintsActive($slug)
    {
        $data = Complaint::where('slug',$slug)->first();
       
    
            if($data->status == 1){
                $data->status = 0;
            }
            else{
                $data->status = 1;
            }
            $data->save();
            
            return back();
        
    }

    public function complaintsUpdate(ComplaintSaveRequest $request)
    {

        $data = $request->all();
        
        $complaint =Complaint::where('slug',$request->slug)->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'language' => $data['language'],
            'category' => $data['category'],
        ]);
    
        return response()->json(['message' =>'success', 'complaint' =>$complaint], 200);

    }
    

    public function tripComplaint(Request $request)
    {
        $languages = Languages::where('status',1)->get();
        $tripcomplaint = Tripcomplaint::get();
        return view('taxi.complaints.TripComplaints',['tripcomplaint' => $tripcomplaint,'languages' => $languages]);
    }

    public function tripComplaintsave(TripComplaintSaveRequest $request) 
    {
        $data = $request->all();
        $tripcom = Tripcomplaint::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'type' => $data['type'],
            //  'complaint_type' => $data['complaint_type'], 
            'language' => $data['language'],
            //  'status' =>$data['status'],
        ]);
        return response()->json(['message' =>'success'], 200);
    }
    public function tripcomplaintsUpdate(TripComplaintSaveRequest $request)
    {

        $data = $request->all();
        
        $complaint =Tripcomplaint::where('slug',$request->slug)->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'category' => $data['category'],
        ]);
    
        return response()->json(['message' =>'success', 'complaint' =>$complaint], 200);

    }

    public function tripcomplaintsEdit($slug)
    {
        
        $complaint = Tripcomplaint::where('slug',$slug)->first();
        
               
        return response()->json(['message' =>'success','complaint' => $complaint], 200);
    }

    public function tripcomplaintsDelete($slug)
    {
        $complaint = Tripcomplaint::where('slug',$slug)->first();
        $usercomplaints = UserComplaint::where('tripcomplaint_id',$complaint->id)->count();
        if($usercomplaints> 0)
        {
            session()->flash('message',"Cannot delete this TripComplaint");
            return back();
        }
        $complaint = Tripcomplaint::where('slug',$slug)->delete();

        return redirect()->route('tripComplaint');
    }
    public function tripcomplaintsActive($slug)
    {
        $data = Tripcomplaint::where('slug',$slug)->first();
       
    
            if($data->status == 1){
                $data->status = 0;
            }
            else{
                $data->status = 1;
            }
            $data->save();
            
            return back();
        
    }
}
