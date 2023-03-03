<?php

namespace App\Http\Controllers\Taxi\Web\OutstationMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\OutstationPackage;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Requests\Request as RequestModel;

class OutstationpackageController extends Controller
{
   

    public function outstationPackage(Request $request)
    {
        $outstationpackage = OutstationPackage::get();
        $vehicle = Vehicle::all();

        return view('taxi.outstation-master.outstationpackage',['outstationpackage' => $outstationpackage,'vehicle' =>$vehicle]);
    }
    public function outstationPackageedit($id)
    {
        $Outstation = OutstationPackage::with('getVehicletype')->where('id',$id)->first();
        return response()->json(['message' =>'success','Outstation' => $Outstation], 200);
    }
    public function outstationPackageSave(Request $request)
    {
        $data = $request->all();
        $vehicleModel = Vehicle::where('slug',$data['vehicle_type'])->first();
        $OutstationPack = OutstationPackage::create([
            'base_price'=>$data['base_price'],
            'driver_bata'=>$data['driver_bata'],
            'price_per_km'=>$data['price_per_km'],
            'hours'=>$data['hours'],
            'package_name'=>$data['package_name'],
            'vehicle_type' => $vehicleModel->id,
            'status' => 1,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function outstationPackageUpdate(Request $request)
    {
        $data = $request->all();
        $vehicleModel = Vehicle::where('slug',$data['vehicle_type'])->first();
        $OutstationPack = OutstationPackage::where('id',$data['id'])->first();

        $OutstationPack->base_price =$data['base_price'];
        $OutstationPack->driver_bata =$data['driver_bata'];
        $OutstationPack->price_per_km =$data['price_per_km'];
        $OutstationPack->hours =$data['hours'];
        $OutstationPack->package_name =$data['package_name']; 
        $OutstationPack->vehicle_type = $vehicleModel->id;    
        $OutstationPack->save();        
        return response()->json(['message' =>'success'], 200);

    }
    public function outstationPackageDelete($id)
    {
        $OutstationMaster = OutstationPackage::where('id',$id)->first();
        $OutstationMaster = OutstationPackage::where('id',$id)->delete();
        return redirect()->route('outstation-package');
    }
    public function outstationPackageChangeStatus($id)
    {
        $OutstationMaster = OutstationPackage::where('id',$id)->first();

        if($OutstationMaster->status == 1){
            $OutstationMaster->status = 0;
        }
        else{
            $OutstationMaster->status = 1;
        }
        $OutstationMaster->save();
        return redirect()->route('outstation-package');
    }
}