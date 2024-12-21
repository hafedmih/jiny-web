<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use App\Http\Controllers\API\BaseController;
use App\Models\taxi\ErrorLog;
use Validator;
use DB;




class ErrorlogController extends Controller
{
    // openJSONFile($language->code);

    public function index(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'error' => 'required', 
           
        ]);

        $log = new ErrorLog();
        $log->error = $request['error'];
        $log->save();


         return response()->json([
         "success" => true,
         "message" => "Error Log List",
         
          ]);
       // return "hello";

    }

    //  public function  newfile()
    //  {
         
    //  }
}
//         try{
//             $jsonString = [];
//             if(File::exists(base_path('public/errorlog/mob_'.error.'.json'))){
//                 $jsonString = file_get_contents(base_path('public/errorlog/mob_'.error.'.json'));
//                 $jsonString = json_decode($jsonString, true);
//                 return $this->sendResponse('Data Found',$jsonString,200);  
//             }
//             else
//             {
//                 return $this->sendError('No Data Found',[],404);
//             }
//         }
// }
