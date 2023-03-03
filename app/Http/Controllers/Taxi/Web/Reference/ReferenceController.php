<?php

namespace App\Http\Controllers\Taxi\Web\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Driver;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\VehicleModel;
use App\Traits\CommanFunctions;
use App\Models\taxi\Requests\RequestPlace;

class ReferenceController extends Controller
{
    use CommanFunctions;
    public function reference(Request $request)
    {
        
        $vehicle = array();
        $vehicle_model_c = array();
        $available_vehicles =Vehicle::where('status',1)->pluck('vehicle_name','id')->toArray();
        foreach ($available_vehicles as $key => $value) {

            $vehiclemodel =Driver::where('type',$key)->where('status',1)->count();
            array_push($vehicle, $vehiclemodel);
            $vehicle_model =Driver::where('type',$key)->count();
            array_push($vehicle_model_c, $vehicle_model);
        }
        $vehicle_model_count = array_combine(range(1, count($vehicle_model_c)), array_values($vehicle_model_c));
         $vehicleList = Vehicle::where('status',1)->get();

         $trip_count = RequestModel::Join('zone_price', 'requests.zone_type_id', '=' ,'zone_price.id')->where('is_completed',1)->where('type_id',1)->get();
        //  $vehicles = Vehicle::join('zone_price','vehicle.id','=','zone_price.type_id')->where('type_id',2)->count(); 
        
        $vehicles['auto']=9;
        $vehicles['mini']=5;
        $vehicles['sedan']=2;
        $vehicles['suv']=2;
        // dd($vehicle);
        
        
    //  dd($trip_count);

        return view('taxi.reference.index',['vehicles' => $vehicles,'vehicle' => $vehicle,'vehicle_model_count' =>  $vehicle_model_count,'available_vehicles' => $available_vehicles,'vehicleList' =>  $vehicleList,]);
    }

    public function mapView(Request $request)
    {
        $point = RequestPlace::get();
        return view('taxi.reference.mapview',['point' => $point]);
   
    }

   

   

}
