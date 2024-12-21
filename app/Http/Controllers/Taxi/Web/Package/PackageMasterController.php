<?php

namespace App\Http\Controllers\Taxi\Web\Package;

use App\Http\Controllers\Controller;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Requests\Request as RequestModel;
use Illuminate\Http\Request;
use App\Models\boilerplate\Country;
use App\Traits\RandomHelper;
use App\Http\Requests\Taxi\Web\PackageSaveRequest;
use DB;


class PackageMasterController extends Controller
{

    use RandomHelper;

    function __construct()
    {
        $this->middleware('permission:new-subscription', ['only' => ['submasterSave']]);
        $this->middleware('permission:edit-subscription', ['only' => ['submasterEdit','submasterUpdate']]);
        $this->middleware('permission:delete-subscription', ['only' => ['submasterDelete']]);
    }
  
    public function packagelist()
    {
        $packagelist = PackageMaster::get();
        $packageitemlist = PackageMaster::get();
        $country = Country::where('status',1)->get();

        $vehicle = Vehicle::all();

        return view('taxi.package.index', ['packagelist' => $packagelist,'vehicle' =>$vehicle,'packageitemlist'=>$packageitemlist,'country'=>$country]);
        
    }

    public function packagecreate(Request $request)
    {
        $vehicle = Vehicle::where('service_type','like','%rental%')->get();
        $country = Country::where('status',1)->get();
        return view('taxi.package.create',compact('vehicle','country'));
    }


    public function packageSave(PackageSaveRequest $request)
    {
        $data = $request->all();

    //   DB::beginTransaction();

    //   try {

        $packmaster = PackageMaster::where('is_base_package','=','YES')->get();
        // dd($packmaster);
        if(count($packmaster)>0)
        {
            $packagemaster = PackageMaster::create([
                'name' => $data['name'],
                'hours' => $data['hours'],
                'km' => $data['km'],
                'admin_commission_type' => $data['admin_commission_type'],
                'admin_commission' => $data['admin_commission'],
                'driver_price' => $data['driver_price'],
                // 'time_cast' => $data['time_cast'],
                 'time_cast_type' => $data['time_cast_type'],
                //  if($packmaster->is_base_package == 'YES')
                // {
                //     return back()->with('success', 'package cannot be changed');                
                // }
                 'country' => $data['country'],
                // 'distance_cast' => $data['distance_cast'],
                'is_base_package' => 'NO',
                
            ]);
            return redirect()->route('packagelist')->with('success', 'One Base package already activated'); 
        }else
        {
            $packagemaster = PackageMaster::create([
                'name' => $data['name'],
                'hours' => $data['hours'],
                'km' => $data['km'],
                'admin_commission_type' => $data['admin_commission_type'],
                'admin_commission' => $data['admin_commission'],
                'driver_price' => $data['driver_price'],
                // 'time_cast' => $data['time_cast'],
                 'time_cast_type' => $data['time_cast_type'],
                 'is_base_package'=> $data['package'],
                 'country' => $data['country']
                // 'distance_cast' => $data['distance_cast'],
            ]);
        }
        

        foreach ($request->type_id as $key => $value) {
            PackageItem::create([
                'package_id' => $packagemaster->id,
                'type_id' => $request->type_id[$key],
                'price' => $request->price[$key]
            ]);
        }

        // DB::commit();
    // } catch (\Exception $e) {
    //     DB::rollback();
    // }
        return redirect()->route('packagelist');
}


    public function packageEdit($slug)
    {

        $vehicle = Vehicle::where('service_type','like','%rental%')->get();

        $packagelist = PackageMaster::where('slug',$slug)->first();

        foreach ($vehicle as $key => $value) {
            $packageitemlist = PackageItem::where('package_id',$packagelist->id)->where('type_id',$value->id)->first();
            $vehicle[$key]->amount = $packageitemlist ? $packageitemlist->price : '0';
        }


        return view('taxi.package.edit',compact('packagelist','vehicle'));
    }


    public function packageUpdate(PackageSaveRequest $request)
    {
        $data = $request->all();
        // DB::beginTransaction();

        // try {

            // dd($data);

            $packmaster = PackageMaster::where('is_base_package','=','YES')->get();
            if(count($packmaster)>0)
            {
                $packagelist = PackageMaster::where('slug',$data['id'])->first();
                $packagelist->name = $data['name'];
                $packagelist->hours = $data['hours'];
                $packagelist->km = $data['km'];
                $packagelist->admin_commission_type = $data['admin_commission_type'];
                $packagelist->admin_commission = $data['admin_commission'];
                $packagelist->driver_price = $data['driver_price'];
                // $packagelist->time_cast = $data['time_cast'];
                $packagelist->time_cast_type = $data['time_cast_type'];
                // $packagelist->distance_cast = $data['distance_cast'];
                $packagelist->is_base_package = $data['package'];
                // if($packagelist->is_base_package == 'YES'){
                //     return back()->with('success', 'package cannot be changed');
                    
                // }elseif($packagelist->is_base_package == 'NO'){
                //     $packagelist->is_base_package = $data['package'];
                    
            
                // }
                
                if($packagelist->is_base_package == 'NO'){
                    $packagelist->is_base_package = $data['package'];
                    $msg = 'Base package changed to no';
                }else{
                    $packagelist->is_base_package = 'NO';
                    $msg = 'Should be only one base package';
                }
                $packagelist->save();
                // return redirect()->route('packagelist')->with('success', 'One Base package already activated');
            }else
            {
                $packagelist = PackageMaster::where('slug',$data['id'])->first();
                $packagelist->name = $data['name'];
                $packagelist->hours = $data['hours'];
                $packagelist->km = $data['km'];
                $packagelist->admin_commission_type = $data['admin_commission_type'];
                $packagelist->admin_commission = $data['admin_commission'];
                $packagelist->driver_price = $data['driver_price'];
                // $packagelist->time_cast = $data['time_cast'];
                $packagelist->time_cast_type = $data['time_cast_type'];
                // $packagelist->distance_cast = $data['distance_cast'];
                $packagelist->is_base_package = $data['package'];
                // if($packagelist->is_base_package == 'YES'){
                //     return back()->with('success', 'package cannot be changed');
                    
                // }elseif($packagelist->is_base_package == 'NO'){
                //     $packagelist->is_base_package = $data['package'];
                    
            
                // }
                $packagelist->save();
                // if($packagelist->is_base_package == 'YES'){
                //     $msg = ' changed1';
                // }else{
                //     $msg = ' cannot changed1';
                // }
                 $msg = ' Base package changed to yes';
                //  dd($packagelist);
            }

            

            foreach ($request->type_id as $key => $value) {

                $PackageItem = PackageItem::where('package_id',$packagelist->id)->where('type_id',$request->type_id[$key])->first();
                if(!$PackageItem){
                    PackageItem::create([
                        'package_id' => $packagelist->id,
                        'type_id' => $request->type_id[$key],
                        'price' => $request->price[$key]
                    ]);
                }
                else{
                    $PackageItem->price = $request->price[$key];
                    $PackageItem->save();
                }
            }

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollback();
        // }

        return redirect()->route('packagelist')->with('success',$msg);
    }

 

    public function packageDelete($slug)
    {        
        $packmasterlist = PackageMaster::where('slug',$slug)->first();
        // dd($packmasterlist->id);
        // $submasterlist = PackageMaster::whereId($submasterlist->id)->delete(); 
        $pack = RequestModel::where('package_id',$packmasterlist->id)->get();
        $data = ['cannot delete'];  
        if(count($pack)>0){
            session()->flash('message','Cannot delete the package');
            session()->flash('status',false);
             return back();       
        }else{ 
            $packmasterlist = PackageMaster::whereId($packmasterlist->id)->delete();            
            return back();
        }              

        return redirect()->route('packagelist');
    }

    public function packageStatusChange($id)
    {
        $data = PackageMaster::where('slug',$id)->first();

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