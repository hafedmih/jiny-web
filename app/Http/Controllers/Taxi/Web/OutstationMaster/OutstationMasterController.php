<?php

namespace App\Http\Controllers\Taxi\Web\OutstationMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\Requests\RequestBill;
use App\Models\taxi\Vehicle;
use App\Models\boilerplate\Country;
use App\Models\taxi\Requests\Request as RequestModel;
use DB;

class OutstationMasterController extends Controller
{
   

    public function outstationMaster(Request $request)
    {
        $currency = RequestModel::pluck('requested_currency_symbol')->first();
        $outstationlist = OutstationMaster::get();
        $vehicle = Vehicle::all();
        $country = Country::where('status',1)->get();
        
        return view('taxi.outstation-master.index',['outstationlist' => $outstationlist,'currency'=>$currency,'vehicle' => $vehicle,'country' =>$country]);
    }

    public function outstationSave(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $OutstationMaster = OutstationMaster::create([
            'pick_up'=>$data['pick_up'],
            'pick_lat'=>$data['pick_lat'],
            'pick_lng'=>$data['pick_lng'],
            'drop'=>$data['drop'],
            'drop_lat'=>$data['drop_lat'],
            'drop_lng'=>$data['drop_lng'],
            'distance'=>$data['distance'],
            // 'price'=>$data['price'],
            'hill_station'=>$data['hill_station'],
            'country'=>$data['country'],
            // 'type'=>$data['type'],
            // 'waiting_time'=>$data['waiting_time'],
            // 'driver_bata'=>$data['driver_bata'],
            // 'base_price'=>$data['base_price'],
            'status' => 1,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function outstationEdit($id)
    {
        $Outstation = OutstationMaster::where('id',$id)->first();
        
        
        return response()->json(['message' =>'success','Outstation' => $Outstation], 200);
    }

    public function outstationDelete($id)
    {
        $OutstationMaster = OutstationMaster::where('id',$id)->first();

        $request = DB::table('requests')
                 ->where('outstation_id',$OutstationMaster->id)
                 ->get();
        $data = ['cannot delete'];  
        if(count($request)>0){
            session()->flash('message','Cannot delete the Route');
            session()->flash('status',false);
             return back();       
        }else{
            $OutstationMaster = OutstationMaster::whereId($OutstationMaster->id)->delete();            
            return back();
        } 

        // $OutstationMaster = OutstationMaster::where('id',$id)->delete();
        return redirect()->route('out-station');
    }

    public function outstationChangeStatus($id)
    {
        $OutstationMaster = OutstationMaster::where('id',$id)->first();

        if($OutstationMaster->status == 1){
            $OutstationMaster->status = 0;
        }
        else{
            $OutstationMaster->status = 1;
        }
        $OutstationMaster->save();
        return redirect()->route('out-station');
    }

    public function outstationUpdate(Request $request)
    {
        $data = $request->all();

        $OutstationMaster = OutstationMaster::where('id',$data['id'])->first();

        $OutstationMaster->pick_up =$data['pick_up'];
        $OutstationMaster->pick_lat =$data['pick_lat'];
        $OutstationMaster->pick_lng =$data['pick_lng'];
        $OutstationMaster->drop =$data['drop'];
        $OutstationMaster->drop_lat =$data['drop_lat'];
        $OutstationMaster->drop_lng =$data['drop_lng'];
        $OutstationMaster->distance =$data['distance'];
        $OutstationMaster->hill_station =$data['hill_station'];
        // $OutstationMaster->price =$data['price'];
        // $OutstationMaster->waiting_time =$data['waiting_time'];
        // $OutstationMaster->driver_bata =$data['driver_bata'];
        // $OutstationMaster->base_price =$data['base_price'];
        // $OutstationMaster->type =$data['type'];

        
        

        $OutstationMaster->save();        

        return response()->json(['message' =>'success'], 200);

    }

    public function outstationSetPrice(Request $request)
    {
        $OutstationMaster = Vehicle::where('status',1)->where('service_type','like','%outstation%')->get();
        $currency = RequestModel::pluck('requested_currency_symbol')->first();
        return view('taxi.outstation-master.OutstationSetPrice',['OutstationMaster' => $OutstationMaster,'currency'=> $currency]);
    }

    public function outstationSetPriceedit($id)
    {
        $Outstation = OutstationPriceFixing::where('type_id',$id)->first();

        $type_id['type_id'] = $id;
        
        return response()->json(['message' =>'success','Outstation' => $Outstation,'type_id' => $type_id], 200);
    }

    public function outstationSetPriceSave(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // foreach ($data as $key => $value) {
            $outstation_price = OutstationPriceFixing::where('type_id',$data['type_id'])->first();

            if(!$outstation_price){
                $outstation_price = new OutstationPriceFixing();
                $outstation_price->type_id = $data['type_id'];
            }
            $outstation_price->distance_price = $data['distance_price'];
            $outstation_price->distance_price_two_way = $data['distance_price_two_way'];
            $outstation_price->admin_commission_type = $data['admin_commission_type'];
            $outstation_price->admin_commission = $data['admin_commission'];
            $outstation_price->driver_price = $data['driver_price'];
            $outstation_price->grace_time = $data['grace_time'];
            $outstation_price->day_rent_two_way = $data['day_rent_two_way'];
            $outstation_price->hill_station_price = $data['hill_station_price'];
            $outstation_price->waiting_charge = $data['waiting_charge'];
            $outstation_price->base_fare = $data['base_fare'];
            $outstation_price->minimum_km = $data['minimum_km']; 
            $outstation_price->save();
        // }
            // dd($outstation_price);
        return response()->json(['message' =>'success'], 200);
    }
}
