<?php

namespace App\Http\Controllers\Taxi\Web\Sos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Sos;
use App\Http\Requests\Taxi\Web\SOSSaveRequest;
use App\Models\boilerplate\Languages;
use Carbon\Carbon;
use File;
use DB;
class SosController extends Controller

{

    function __construct()
    {
        $this->middleware('permission:new-sos', ['only' => ['sosSave']]);
        $this->middleware('permission:edit-sos', ['only' => ['sosEdit','sosUpdate']]);
        $this->middleware('permission:delete-sos', ['only' => ['sosDelete']]);
        $this->middleware('permission:status-change-sos', ['only' => ['sosChangeStatus']]);
    }

    public function sos(Request $request)
    {
        $languages = Languages::where('status',1)->get();
        $columns = [];
      
       if($languages->count() > 0){
           foreach ($languages as $key => $language){
               $columns[$language->code] = $this->openJSONFile($language->code);
           }
       }
        $sosList = json_decode(json_encode($columns),true);

        
        return view('taxi.sos-management.sos-management',['sosList' => $sosList],['languages' => $languages]);
    }

     public function sosSave(SOSSaveRequest $request)
    {
         $data = $request->all();
         $sos = Sos::create([
            'phone_number' => $data['phone_number'],
            'title' => $data['title'],
            'language' =>$data['language'],
            'status' => $data['status'],
            'created_by' => NULL,
            'slug' => Carbon::now()->timestamp
         ]);
         $data = $this->openJSONFile($request->language);
        
            $data[$sos->slug] = $sos; 
            $this->saveJSONFile($request->language, $data);
        return response()->json(['message' =>'success'], 200);

    }


    public function sosEdit(Request $request,$key)
    {
        $languages = DB::table('languages')->get();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key])){
                    $sos=$data[$key];
                }
            }
        }
        return response()->json(['message' =>'success','sos' => $sos,'key'=>$key], 200);
        
    }

    public function sosView($id)
    {
        $sos = Sos::where('slug',$id)->first();
        return response()->json(['message' =>'success','sos' => $sos], 200);
    }


    public function sosDelete($key)
    {
        
        $languages = DB::table('languages')->get();

        if($languages->count() > 0){
            foreach ($languages as $language){
                 $data = $this->openJSONFile($language->code);
                        unset($data[$key]);
                  $this->saveJSONFile($language->code, $data);
            }
            
        }
        return redirect()->route('sos-management');
    }


    public function sosUpdate(Request $request)
    {
        $languages = DB::table('languages')->get();

        $sos = $request->all();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
               
                if (isset($data[$request->sos_id])){
                    $s = $data[$request->sos_id];  
                    if($s > 0){
                        $s['phone_number'] = $sos['phone_number'];
                        $s['title'] = $sos['title'];
                    }
                   $data[$request->sos_id]= $s;

                  $this->saveJSONFile($language->code, $data);
                }
            }
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function sosChangeStatus($key)
    {
        $languages = DB::table('languages')->get();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key])){

                   $sos = $data[$key];
                    if($sos['status'] == "1"){
                        $sos['status'] = "0";
                    }
                    else{
                        $sos['status'] = "1";
                    }
                    $data[$key]= $sos;
                    $this->saveJSONFile($language->code, $data);
                    }
                 
            }
        }
        return redirect()->route('sos-management');
    }
    private function openJSONFile($code){
        $jsonString = [];
        if(File::exists(base_path('resources/lang/sos_'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/sos_'.$code.'.json'));
            $jsonString = json_decode($jsonString, true);
        }
        return $jsonString;
    }
    /**
     * Save JSON File
     * @return Response
    */
    private function saveJSONFile($code, $data){
        ksort($data);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path('resources/lang/sos_'.$code.'.json'), stripslashes($jsonData));
       
       
    }
}