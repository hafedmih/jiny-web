<?php


namespace App\Http\Controllers\boilerplate\Web;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\boilerplate\Country;
use DB;
use File;


class CountryController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:status-change-country', ['only' => ['activeCountry']]);
    }

   public function list()
   {
        $country= Country::get();
        // dd($country);
        return view('boilerplate.country.country', compact('country'));
   }
    

    public function activeCountry($id)
    {
        $country = Country::where('id',$id)->first();

        if($country->status == 1){
            $country->status = 0;
        }
        else{
            $country->status = 1;
        }
        $country->save();

        return redirect()->route('country');
    }

}



