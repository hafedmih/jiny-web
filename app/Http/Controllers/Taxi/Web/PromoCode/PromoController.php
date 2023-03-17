<?php

namespace App\Http\Controllers\Taxi\Web\PromoCode;

use App\Http\Controllers\Controller;
use App\Models\taxi\Driver;
use Illuminate\Http\Request;
use App\Models\taxi\Promocode;
use App\Models\taxi\Zone;
use App\Models\taxi\Vehicle;
use App\Models\User;
use App\Traits\RandomHelper;
use App\Models\taxi\Requests\Request as RequestModel;



class PromoController extends Controller
{

    use RandomHelper;

    function __construct()
    {
        $this->middleware('permission:new-promocode', ['only' => ['Promocreate','Promosave']]);
        $this->middleware('permission:delete-promocode', ['only' => ['promoDelete']]);
        $this->middleware('permission:status-change-promocode', ['only' => ['promoActive']]);
    }
  
    public function Promolist()
    {
        $promolist = Promocode::orderBy('id','desc')->get();
        $currency = RequestModel::pluck('requested_currency_symbol')->first();
        return view('taxi.promocode.index', ['promolist'=>$promolist,'currency'=>$currency]);
        
    }

    public function Promocreate(Request $request)
    {
        $zone = Zone::get();

        $vehicleList = Vehicle::orderBy('sorting_order', 'ASC')->get();
        $user = User::role('user')->get();
       
        return view('taxi.promocode.create', compact('zone','user','vehicleList'));
    }

    public function generatePromo()
    {
        do{
            $promocode = "PROMO100-".$this->RandomString(6);
        } while (Promocode::where('promo_code', '=', $promocode)->exists());

        return response()->json(['promo' => $promocode]);
    }

    public function promoEdit($id)
    {
        $promolist = Promocode::where('slug',$id)->first();
        $zone = Zone::get();
        $vehicleList = Vehicle::orderBy('sorting_order', 'ASC')->get();

        $user = User::role('user')->get();
        return view('taxi.promocode.edit', compact('promolist','zone','user','vehicleList'));
    }

    public function Promosave(Request $request)
    {
        $this->validate($request, [
            'zone_id'=>'required',
            'promo_code' => 'required|unique:promocode',
            'promo_icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'target_amount' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|digits_between:1,15',
            'promo_type' => 'required',
            'select_offer_option' => 'required',                     
            // 'types' => 'required'                       
        ]); 
        $filename =  uploadImage('promo',$request->file('promo_icon'));

        // $types = Vehicle::whereIn('slug',$request['types'])->pluck('id')->toArray();
        $promo = new Promocode();
        $promo->zone_id = strip_tags(trim($request->input('zone_id')));
        $promo->promo_code = strip_tags(trim($request->input('promo_code')));
        $promo->select_offer_option = $request->input('select_offer_option');
        $promo->promo_icon = $filename;
        $promo->description = strip_tags(trim($request->input('description')));
        $promo->target_amount = strip_tags(trim($request->input('target_amount')));
        $promo->promo_type = strip_tags(trim($request->input('promo_type')));
        $promo->amount = strip_tags(trim($request->input('amount')));
        $promo->percentage = strip_tags(trim($request->input('percentage')));
        $promo->from_date = strip_tags(trim($request->input('from_date')));
        $promo->to_date = strip_tags(trim($request->input('to_date')));
        // $promo->types_id = $request['types'] ? implode(",",$types) : NULL;
        $promo->user_id = $request['user_id'] ? implode(",",$request['user_id']) : NULL;
        $promo->promo_use_count = $request['promo_use_count'] ? $request['promo_use_count'] : 0;
        $promo->promo_user_reuse_count = $request['promo_user_reuse_count'] ? $request['promo_user_reuse_count'] : 0;
        $promo->new_user_count = $request['new_user_count'] ? $request['new_user_count'] : 0;
        // $promo->total_promo_limit_count = $request['total_promo_limit_count'];
        $promo->save();

        return redirect()->route('promolist')->with('success','added successfuly');

    }

    public function PromoUpdate(Request $request)
    {
        // $this->validate($request, [
        //     'target_amount' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|digits_between:1,15',
        //     'promo_type' => 'required',                     
        // ]); 

        // $types = Vehicle::whereIn('slug',$request['types'])->pluck('id')->toArray();
        $promo = Promocode::where('slug',$request->promo_slug)->first();
        $promo->description = strip_tags(trim($request->input('description')));
        $promo->target_amount = strip_tags(trim($request->input('target_amount')));
        $promo->promo_type = strip_tags(trim($request->input('promo_type')));
        $promo->amount = strip_tags(trim($request->input('amount')));
        $promo->percentage = strip_tags(trim($request->input('percentage')));
        $promo->from_date = strip_tags(trim($request->input('from_date')));
        $promo->to_date = strip_tags(trim($request->input('to_date')));
        // $promo->types_id = $request['types'] ? implode(",",$types) : NULL;
        $promo->user_id = $request['user_id'] ? implode(",",$request['user_id']) : NULL;
        $promo->promo_use_count = $request['promo_use_count'] ? $request['promo_use_count'] : 0;
        $promo->promo_user_reuse_count = $request['promo_user_reuse_count'] ? $request['promo_user_reuse_count'] : 0;
        $promo->new_user_count = $request['new_user_count'] ? $request['new_user_count'] : 0;
        // $promo->total_promo_limit_count = $request['total_promo_limit_count'];
        $promo->save();

        return redirect()->route('promolist')->with('success','Updated successfuly');

    }


    public function promoDelete($id)
    {
        $promocode = Promocode::where('slug',$id)->first();
        $promo = RequestModel::where('promo_id',$promocode->id)->count();
        
        if($promo > 0){
            session()->flash('message',"This Promocode cannot be deleted,its already in use");
            return back();
        }
        $promocode = Promocode::where('slug',$id)->delete();
        return redirect()->route('promolist');
    }

    public function promoActive($id)
    {
        $promocode = Promocode::where('slug',$id)->first();

        if($promocode->status == 1){
            $promocode->status = 0;
        }
        else{
            $promocode->status = 1;
        }
        $promocode->save();
        return redirect()->route('promolist');
    }
}
