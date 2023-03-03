<?php

namespace App\Http\Controllers\Taxi\Web\VehicleModel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\VehicleModelRequest;

use App\Models\taxi\VehicleModel;
use App\Models\taxi\Vehicle;

use Validator;
use Redirect;

class VehicleModelController extends Controller
{


    public function vehiclemodel(Request $request)
    {
        $vehicleList = VehicleModel::all();
        $vehicle = Vehicle::all();
        return view('taxi.vehicle-model.vehicle',['vehicleList' => $vehicleList,'vehicle' => $vehicle]);
    }

    public function index()
    {
        return view('taxi.vehicle-model.index');
    }

    public function vehicle(Request $request)
    {
        $vehicleList = VehicleModel::all();
        $vehicle = Vehicle::all();
        
            // dd($vehicle);
            // // $category = Category::all();

        return view('taxi.vehicle-model.vehicle',['vehicleList' => $vehicleList,'vehicles' => $vehicle ]);
    }

    public function vehicleModelStore(VehicleModelRequest $request)
    {
        $data = $request->all();
        // dd($data);
        $vehicleModel = Vehicle::where('slug',$data['vehicle_id'])->first();
        // dd($vehicleModel);
       
        // $filename =  uploadImage('images/vehiclesmodel',$request->file('image'));

        $vehicleadd = VehicleModel::create([
            'model_name' => $data['model_name'],
            'description' => $data['description'],
            'vehicle_id' => $vehicleModel->id,
            // 'image' => $filename,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function vehicleModelEdit($id)
    {
        $vehicle = VehicleModel::with('getVehicle')->where('slug',$id)->first();

        return response()->json(['message' =>'success','vehicle' => $vehicle], 200);
    }

    public function vehicleModelUpdate(Request $request)
    {
        $data = $request->all();
        $vehiclemodel = Vehicle::where('slug',$data['vehicle_id'])->first();

       // dd($vehiclemodel);
        // $category = Category::where('slug',$data['category_id'])->first();


        $vehicle = VehicleModel::where('slug',$data['vehiclemodel_id'])->update([
            'model_name' => $data['model_name'],
            'description' => $data['description'],
            'vehicle_id' => $vehiclemodel->id
            // 'image' => $filename,
        ]);

       //dd($vehicle);
        // $image = Vehicle::where('slug',$data['vehicle_id'])->first();
        
        // if($request->file('image')){
        //     $filename =  uploadImage('images/vehiclesmodel',$request->file('image'));
        //     $image->image = $filename;
        //     $image->save();
        // }

            // dd($data['vehicle_id']);
            return response()->json(['message' =>'success'], 200);
    }

    public function vehicleModelDelete($id)
    {
        $vehicle = VehicleModel::where('slug',$id)->first();
        
        // unlink(\Storage::path('public/images/vehiclesmodel/'.$vehicle->image));
        $vehicle = VehicleModel::where('slug',$id)->delete();
        return back();
    }

    public function vehicleModelStatusChange($id)
    {
        $data = VehicleModel::where('slug',$id)->first();
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