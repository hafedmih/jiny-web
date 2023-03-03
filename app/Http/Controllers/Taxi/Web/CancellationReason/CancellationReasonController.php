<?php

namespace App\Http\Controllers\Taxi\Web\CancellationReason;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\CancellationReason;
use App\Http\Requests\Taxi\Web\CancellationReasonSaveRequest;

class CancellationReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:new-cancelreason', ['only' => ['cancelReasonSave']]);
        $this->middleware('permission:edit-cancelreason', ['only' => ['cancelReasonEdit','cancelReasonUpdate']]);
        $this->middleware('permission:delete-cancelreason', ['only' => ['cancelReasonDelete']]);
        $this->middleware('permission:active-cancelreason', ['only' => ['cancelReasonChangeStatus']]);
    }

    public function index(Request $request)
    {
        $cancellationReasons = CancellationReason::get();
        return view('taxi.cancellation-reasons.index',['cancellationReasons' => $cancellationReasons]);
    }

    public function cancelReasonSave(CancellationReasonSaveRequest $request)
    {
        $data = $request->all();
        $cancellation = CancellationReason::create([
            'user_type' => $data['reason_type'],
            'reason' => $data['cancellation_reason'],
            'trip_status' => $data['trip_status'],
            'pay_status' => $data['pay_status'],
            'active' => 1,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function cancelReasonEdit($id)
    {
        $cancellation = CancellationReason::where('slug',$id)->first();

        return response()->json(['message' =>'success','cancellation' => $cancellation], 200);
    }

    public function cancelReasonDelete($id)
    {
        $category = CancellationReason::where('slug',$id)->delete();
        return redirect()->route('cancellationReason');
    }

    public function cancelReasonChangeStatus($id)
    {
        $category = CancellationReason::where('slug',$id)->first();

        if($category->active == 1){
            $category->active = 0;
        }
        else{
            $category->active = 1;
        }
        $category->save();
        return redirect()->route('cancellationReason');
    }

    public function cancelReasonUpdate(CancellationReasonSaveRequest $request)
    {
        $data = $request->all();

        $category = CancellationReason::where('slug',$data['reason_id'])->update([
            'user_type' => $data['reason_type'],
            'reason' => $data['cancellation_reason'],
            'trip_status' => $data['trip_status'],
            'pay_status' => $data['pay_status'],
        ]);

        return response()->json(['message' =>'success'], 200);

    }
}
