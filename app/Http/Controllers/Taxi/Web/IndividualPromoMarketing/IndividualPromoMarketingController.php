<?php

namespace App\Http\Controllers\Taxi\Web\IndividualPromoMarketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\IndividualPromoMarketing;
use App\Models\taxi\promo;
use App\Models\taxi\Vehicle;

use Validator;
use Redirect;

class IndividualPromoMarketingController extends Controller
{

    public function index(Request $request)
    {
        $promoList = IndividualPromoMarketing::all();
        
        return view('taxi.individual_promo_marketing.index',['promoList' => $promoList]);
    }

    public function promo(Request $request)
    {
        $promoList = IndividualPromoMarketing::all();
        
        return view('taxi.individual_promo_marketing.index',['promoList' => $promoList]);
    }

    public function promoStore(Request $request)
    {
        $data = $request->all();
       

        $promoadd = IndividualPromoMarketing::create([
            'promo_name' => $data['promo_name'],
            'promo_percentage' => $data['promo_percentage'],
            'promo_amount' => $data['promo_amount'],
            'trip_type' => $data['trip_type'],
            'no_of_times_use' => $data['no_of_times_use'],
            'target_amount' => $data['target_amount'],
            'promo_amount_type' => $data['promo_type'],
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function promoEdit($id)
    {
        $promo = IndividualPromoMarketing::where('slug',$id)->first();

        return response()->json(['message' =>'success','promo' => $promo], 200);
    }

    public function promoUpdate(Request $request)
    {
        $data = $request->all();

       
        $promo = IndividualPromoMarketing::where('slug',$data['promo_id'])->update([
            'promo_name' => $data['promo_name'],
            'promo_percentage' => $data['promo_percentage'],
            'promo_amount' => $data['promo_amount'],
            'trip_type' => $data['trip_type'],
            'no_of_times_use' => $data['no_of_times_use'],
            'target_amount' => $data['target_amount'],
            'promo_amount_type' => $data['promo_type'],
        ]);

      
            return response()->json(['message' =>'success'], 200);
    }

    public function promoDelete($id)
    {
        $vehicle = IndividualPromoMarketing::where('slug',$id)->first();
        

        $vehicle = IndividualPromoMarketing::where('slug',$id)->delete();
        return back();
    }

    public function promoStatusChange($id)
    {
        $data = IndividualPromoMarketing::where('slug',$id)->first();
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