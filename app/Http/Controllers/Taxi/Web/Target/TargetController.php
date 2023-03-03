<?php

namespace App\Http\Controllers\Taxi\Web\Target;

use App\Http\Controllers\Controller;
use App\Models\taxi\Driver;
use Illuminate\Http\Request;
use App\Models\taxi\Target;
use App\Models\taxi\Zone;
use App\Models\User;

class TargetController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:new-target', ['only' => ['TargetSave']]);
        $this->middleware('permission:delete-target', ['only' => ['targetDelete']]);
        $this->middleware('permission:status-change-target', ['only' => ['targetActive']]);
    }
  
    public function Targetlist(Request $request)
    {
        $target_list = Target::all();
        $zone_list = Zone::all();
        $drivers_list = User::role('driver')->get();
        return view('taxi.target.index', compact('target_list','zone_list','drivers_list'));
    }

    public function TargetDrivers(Request $request)
    {
        $driverZoneGet = Driver::where('service_location',$request->ServiceLocation)->with('users')->get();
        return $driverZoneGet;
    }


    public function TargetSave(Request $request)
    {
    
        $target =   request()->validate([
            'target_name'    =>  'required|regex:/^[a-zA-Z ]*$/|max:155',
            'target_icon' => 'mimes:jpeg,jpg,png|required|max:2048',
            'target_driver_type' => 'required',
            'service_location' => 'required',
            // 'achieve_amount' => 'required|numeric',
            // 'no_of_trips' => 'required|numeric'
          ]);
        $driver_id = explode(",",$request['driver_id']);
        $driver = User::whereIn('id',$driver_id)->get();
        $filename =  uploadImage('images/target',$request->file('target_icon'));

        $target = new Target();
        $target->target_name = $request['target_name'];
        $target->target_driver_from_date = $request['target_driver_from_date'];
        $target->target_driver_to_date = $request['target_driver_to_date'];
        $target->target_driver_type = $request['target_driver_type'];
        $target->target_select_package = $request['target_select_package'];
        $target->target_duration = $request['target_duration'];
        $target->achieve_amount = $request['achieve_amount'];
        $target->no_of_trips = $request['no_of_trips'];
        $target->amount = $request['amount'];
        $target->driver_id = $request['driver_id'];
        $target->service_location = $request['service_location'];
        $target->target_icon = $filename;
        
        $target->save();
        
        return response()->json(['message' =>'success'], 200);
        
    }

    public function targetDelete($id)
    {
        $complaint = target::where('slug',$id)->delete();
        return redirect()->route('targetlist');
    }

    public function targetActive($id)
    {
        $complaint = target::where('slug',$id)->first();

        if($complaint->status == 1){
            $complaint->status = 0;
        }
        else{
            $complaint->status = 1;
        }
        $complaint->save();
        return redirect()->route('targetlist');
    }
}
