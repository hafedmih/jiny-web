<?php

namespace App\Http\Controllers\Taxi\Web\Package;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request;
use App\Models\taxi\OutstationPackage;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Requests\Request as RequestModel;

class RentallistController extends Controller
{
   

    public function rentalList(Request $request)
    {
        $rental = Request::where('trip_type','=','RENTAL')->get();
// dd($rental);
        return view('taxi.package.rentallist',['rental' => $rental]);
    }
}