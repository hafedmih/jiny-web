<?php

namespace App\Http\Controllers\Taxi\Web\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\User;
use App\Models\taxi\Zone;
use App\Models\taxi\Category;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonPeriod;
use App\Traits\CommanFunctions;
use DateTime;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\Documents;
use App\Models\taxi\Driver;


class DeleteController extends Controller
{
  public function index(Request $request)
  {
    $data = $request->all();
    $users = Category::get();
    return view('taxi.delete.index', ['users'=>$users]);
  }

  public function destroy(Request $request) 
  {
    $user = User::where('phone_number',$request['phone_number'])->first();
    if($user)
    {
      $user_table_delete     = User::where('id',$user->id)->delete();
      $driver_table_delete   = Driver::where('user_id',$user->id)->delete();
      $document_table_delete = DriverDocument::where('user_id',$user->id)->delete();
      $request_table_delete  = RequestModel::where('user_id',$user->id)->delete();
      $request_table_delete  = RequestModel::where('driver_id',$user->id)->delete();
    }else {
        return redirect()->route('userslist')->with('fail', 'User Not found');

    }
     return redirect()->route('userslist')->with('status', 'User Delete successfully..');
  }

}
