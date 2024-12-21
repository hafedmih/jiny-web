<?php

namespace App\Http\Controllers\Taxi\Web\Outofzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\Outofzone;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\Vehicle;

class OutofzoneController extends Controller
{
   

    public function outofzoneMaster(Request $request)
    {
        $outofzonelist = Outofzone::get();

        return view('taxi.Outofzone.index',['outofzonelist' => $outofzonelist]);
    }

    public function outofzoneSave(Request $request)
    {
        $data = $request->all();

        $OutofzoneMaster = Outofzone::create([
            'kilometer'=>$data['kilometer'],
            'price'=>$data['price'],
            'status' => 1,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function outofzoneEdit($id)
    {
        $OutofzoneMaster = Outofzone::where('id',$id)->first();
        return response()->json(['message' =>'success','outofzoneMaster' => $OutofzoneMaster], 200);
    }

    public function outofzoneDelete($id)
    {
        $OutofzoneMaster = Outofzone::where('id',$id)->first();
        $OutofzoneMaster = Outofzone::where('id',$id)->delete();
        return redirect()->route('outofzone-master');
    }

    public function outofzoneChangeStatus($id)
    {
        $OutofzoneMaster = Outofzone::where('id',$id)->first();

        if($OutofzoneMaster->status == 1){
            $OutofzoneMaster->status = 0;
        }
        else{
            $OutofzoneMaster->status = 1;
        }
        $OutofzoneMaster->save();
        return redirect()->route('outofzone-master');
    }

    public function outofzoneUpdate(Request $request)
    {
        $data = $request->all();

        $OutofzoneMaster = Outofzone::where('id',$data['id'])->first();

        $OutofzoneMaster->kilometer =$data['kilometer'];
        $OutofzoneMaster->price =$data['price'];

        $OutofzoneMaster->save();

        return response()->json(['message' =>'success'], 200);

    }

   
}
