<?php

namespace App\Http\Controllers\Taxi\Web\OutstationMaster;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request;
use App\Models\taxi\Requests\Request as RequestModel;

class OutstationListController extends Controller
{
   

    public function outstationList(Request $request)
    {
        $outstation = Request::where('trip_type','=','OUTSTATION')->get();
        return view('taxi.outstation-master.outstationList',['outstation' => $outstation]);
    }
}