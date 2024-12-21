<?php

namespace App\Http\Controllers\Taxi\Web\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\VehicleSaveRequest;
use App\Models\taxi\VehicleModel;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Category;
use App\Models\taxi\ZonePrice;

use Validator;
use Redirect;

class VehicleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:new-type', ['only' => ['index','vehicleStore']]);
        $this->middleware('permission:edit-type', ['only' => ['vehicleEdit','vehicleUpdate']]);
        $this->middleware('permission:delete-type', ['only' => ['vehicleDelete']]);
        $this->middleware('permission:active-type', ['only' => ['vehicleStatusChange']]);
    }
     
    public function index()
    {
        return view('taxi.vehicle.addvehicle');
    }

    public function vehicle(Request $request)
    {
        $vehicleList = Vehicle::orderBy('sorting_order', 'ASC')->get();
        $category = Category::all();

        return view('taxi.vehicle.vehicle',['vehicleList' => $vehicleList,'category' => $category]);
    }

    public function vehicleStore(VehicleSaveRequest $request)
    {
        $data = $request->all();

        $category = Category::where('slug',$data['category_id'])->first();

        $filename =  uploadImage('images/vehicles',$request->file('image'));

        $highlight_image =  uploadImage('images/vehicles',$request->file('highlight_image'));


        $vehicle = Vehicle::create([
            'vehicle_name' => $data['vehicle_name'],
            'capacity' => $data['capacity'],
            'category_id' => $category->id,
            'sorting_order'=> $data['sorting_order'],
            'image' => $filename,
            'highlight_image' => $highlight_image,
            //'service_type' => $data['service_type'],
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function vehicleEdit($id)
    {
        $vehicle = Vehicle::with('getCategory')->where('slug',$id)->first();

        return response()->json(['message' =>'success','vehicle' => $vehicle], 200);
    }

    public function vehicleUpdate(VehicleSaveRequest $request)
    {
        $data = $request->all();
        $image = Vehicle::where('slug',$data['vehicle_id'])->first();

        $category = Category::where('slug',$data['category_id'])->first();


        $vehicle = Vehicle::where('slug',$data['vehicle_id'])->update([
            'vehicle_name' => $data['vehicle_name'],
            'capacity' => $data['capacity'],
            'sorting_order'=> $data['sorting_order'],
            'category_id' => $category->id,
            //'service_type' => $data['service_type'],
        ]);
        $image = Vehicle::where('slug',$data['vehicle_id'])->first();
        
        if($request->file('image')){
            $filename =  uploadImage('images/vehicles',$request->file('image'));
            $image->image = $filename;
            $image->save();
        }

        if($request->file('highlight_image')){
            $highlight_image =  uploadImage('images/vehicles',$request->file('highlight_image'));
            $image->highlight_image = $highlight_image;
            $image->save();
        }

            //dd($vehicle);
            return response()->json(['message' =>'success'], 200);
    }

    public function vehicleDelete($id)
    {
        $vehicle = Vehicle::where('slug',$id)->first();
        $vehiclemap = VehicleModel::where('vehicle_id',$vehicle->id)->get();
        $chkZoneContainsVehicle = ZonePrice::where('type_id',$vehicle->id)->get();
        $data = ['cannot delete'];  
        if(count($vehiclemap)>0 || count($chkZoneContainsVehicle)>0){
            session()->flash('message','Cannot delete the vehicle');
            session()->flash('status',false);
             return back();       
        }else{
            $vehicle = Vehicle::where('slug',$id)->delete();           
            return back();
        }              
        // unlink(\Storage::path('public/images/vehicles/'.$vehicle->image));
        
        return back();
    }

    public function vehicleStatusChange($id)
    {
        $data = Vehicle::where('slug',$id)->first();

        if($data->status == 1){
            $data->status = 0;
             session()->flash('message','Inactive');
             session()->flash('status',true);
        }
        else{
            $data->status = 1;
            session()->flash('message','Active');
             session()->flash('status',true);
        }
        $data->save();
        
        return back();
        
    }
    
}
