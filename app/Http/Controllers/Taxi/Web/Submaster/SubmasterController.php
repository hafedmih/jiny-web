<?php

namespace App\Http\Controllers\Taxi\Web\Submaster;

use App\Http\Controllers\Controller;
use App\Models\taxi\Submaster;
use Illuminate\Http\Request;
use App\Traits\RandomHelper;
use App\Http\Requests\Taxi\Web\SubmasterSaveRequest;
use DB;
use App\Models\taxi\Requests\Request as RequestModel;


class SubmasterController extends Controller
{

    use RandomHelper;

    function __construct()
    {
        $this->middleware('permission:new-subscription', ['only' => ['submasterSave']]);
        $this->middleware('permission:edit-subscription', ['only' => ['submasterEdit','submasterUpdate']]);
        $this->middleware('permission:delete-subscription', ['only' => ['submasterDelete']]);
    }
  
    public function Submasterlist()
    {
        $submasterlist = Submaster::get();
        $currency = RequestModel::pluck('requested_currency_symbol')->first();
        return view('taxi.subscription-master.index', ['submasterlist' => $submasterlist,'currency'=>$currency]);
        
    }

    public function submasterSave(SubmasterSaveRequest $request)
    {
        $data = $request->all();

        $submasterlist = Submaster::create([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'validity' => $data['validity'],
            'description' => $data['description']
        ]);
        
        return response()->json(['message' =>'success'], 200);
    }

    public function submasterUpdate(SubmasterSaveRequest $request)
    {
        $data = $request->all();
        $submasterlist =Submaster::where('slug',$data['user_id'])->update([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'validity' => $data['validity'],
            'description' => $data['description']
        ]);

        return response()->json(['message' =>'success', 'sublist' =>$submasterlist], 200);
    }

    public function submasterEdit($slug)
    {
        $submasterlist = Submaster::where('slug',$slug)->first();

        return response()->json(['message' =>'success', 'sublist' => $submasterlist], 200);
    }

    public function submasterDelete($slug)
    {
        $submasterlist = Submaster::where('slug',$slug)->delete();

        return redirect()->route('sublist');
    }
}