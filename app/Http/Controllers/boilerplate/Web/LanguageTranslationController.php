<?php


namespace App\Http\Controllers\boilerplate\Web;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\boilerplate\Languages;
use App\Models\User;
use App\Models\taxi\Complaint;
use App\Models\taxi\Faq;
use DB;
use File;


class LanguageTranslationController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @return Response
    */
    public function __construct()
    {
        $this->middleware('permission:add-new-translation', ['only' => ['index']]);
        // $this->middleware('permission:translation-list', ['only' => ['index']]);
        $this->middleware('permission:delete-translation', ['only' => ['destroy']]);
    }

    public function index() {
   	  $languages = Languages::get();
      $columnsCount = Languages::where('status',1)->count();

	    if($languages->count() > 0){
	        foreach ($languages as $key => $language){
                if($language->status == 1){
                    if ($key == 0) {
                        $col[$key] = $this->openJSONFile($language->code);
                    }
                    $col[++$key] = ['data'=>$this->openJSONFile($language->code), 'lang'=>$language->code];
                }
	        }
	    }
      
         //mobile 
        if($languages->count() > 0){
	        foreach ($languages as $key => $language){
                if($language->status == 1){
                    if ($key == 0) {
                        $colmob[$key] = $this->open_mobile_JSONFile($language->code);
                    }
                    $colmob[++$key] = ['data'=>$this->open_mobile_JSONFile($language->code), 'lang'=>$language->code];
                }
	        }
	    }
       
        /** 
        * array key Reindex values with numeric keys
        **/
       
        $columns = array_merge($col);
        $column = array_merge($colmob);
        
   
   	  return view('boilerplate.languages.languages', compact('languages','column','columns','columnsCount'));
    }   
    /**
     * Remove the specified resource from storage.
     * @return Response
    */
    public function store(Request $request){
        $request->validate([
		    'key' => 'required',
		    'value' => 'required',
            'application' => 'required',
		]);
        if($request->application == 1){
		$data = $this->openJSONFile('en');
        $data[$request->key] = $request->value;
        $this->saveJSONFile('en', $data);
        }else{
            $data = $this->open_mobile_JSONFile('en');
        $data[$request->key] = $request->value;
        $this->save_mobile_JSONFile('en', $data);
         /*update time date in  updated_at column */
            $language =  Languages::where('code','en')->first();
            $language->touch();
        }

    


        return redirect()->route('languages');
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
    */
    public function destroy($key)
    {
        $languages = DB::table('languages')->get();


        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                unset($data[$key]);
                $this->saveJSONFile($language->code, $data);
            }
        }
        return response()->json(['success' => $key]);
    }


    /**
     * Open Translation File
     * @return Response
    */
    private function openJSONFile($code){
        $jsonString = [];
        if(File::exists(base_path('resources/lang/'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/'.$code.'.json'));
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
        file_put_contents(base_path('resources/lang/'.$code.'.json'), stripslashes($jsonData));
        $language =  Languages::where('code',$code)->first();
       
    }


    /**
     * Save JSON File
     * @return Response
    */
    public function transUpdate(Request $request){
        $data = $this->openJSONFile($request->code);
        $data[$request->pk] = $request->value;

        $language =  Languages::where('code',$request->code)->first();
        $language->touch();
        $this->saveJSONFile($request->code, $data);
        return response()->json(['success'=>'Done!']);
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
    */
    public function transUpdateKey(Request $request){
        $languages = DB::table('languages')->get();


        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
              
                if (isset($data[$request->pk])){
                    $data[$request->value] = $data[$request->pk];
                    unset($data[$request->pk]);
                    $this->saveJSONFile($language->code, $data);
                }
             
            }
        }
        

        return response()->json(['success'=>'Done!']);
    }

    public function viewLanguage(Request $request)
    {
        $language =  Languages::get();
        
        return view('boilerplate.languages.AddLanguages',['language' => $language]);
    }

    public function editLanguage($id)
    {
        $language =  Languages::where('id',$id)->first();
        return response()->json(['message' =>'success','language' => $language], 200);
    }

    public function saveLanguage(Request $request)
    {
        $data = $request->all();

        $languages = Languages::create([
            'name' => $data['language'],
            'code' => $data['code'],
            'status' => 1
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function updateLanguage(Request $request)
    {
        $data = $request->all();

        $languages = Languages::where('id',$data['language_id'])->update([
            'name' => $data['language'],
            'code' => $data['code'],
            'status' => 1
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function deleteLanguage($id)
    {
        $languages = Languages::where('id',$id)->first(); 
            
        $user = User::where('language',$languages->code)->count();

        $complaint = Complaint::where('language',$languages->code)->count();
        
        $faq = Faq::where('language',$languages->code)->count();
        
        if($user||$complaint||$faq > 0){
            session()->flash('message',"Language cannot be deleted");
            return back();
        }
        $languages = Languages::where('id',$id)->delete();
        return redirect()->route('viewLanguage');
    }

    public function activeLanguage($id)
    {
        $languages = Languages::where('id',$id)->first();

        if($languages->status == 1){
            $languages->status = 0;
        }
        else{
            $languages->status = 1;
        }
        $languages->save();

        return redirect()->route('viewLanguage');
    }

    //mobile JSON 
        private function open_mobile_JSONFile($code){
            $jsonString = [];
            if(File::exists(base_path('public/lang/mob_'.$code.'.json'))){
                $jsonString = file_get_contents(base_path('public/lang/mob_'.$code.'.json'));
                $jsonString = json_decode($jsonString, true);
            }
            return $jsonString;
        }

        /**
         * Save JSON File
         * @return Response
        */
        private function save_mobile_JSONFile($code, $data){
            ksort($data);
            $jsonData = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            file_put_contents(base_path('public/lang/mob_'.$code.'.json'), stripslashes($jsonData));
            $language =  Languages::where('code',$code)->first();
        
        }


        public function m_transUpdate(Request $request){
            $data = $this->open_mobile_JSONFile($request->code);
            $data[$request->pk] = $request->value;
            $language =  Languages::where('code',$request->code)->first();
             $language->touch();         
            
            $this->save_mobile_JSONFile($request->code, $data);
            return response()->json(['success'=>'Done!']);
        }
    
    
        /**
         * Remove the specified resource from storage.
         * @return Response
        */
        public function m_transUpdateKey(Request $request){
            $languages = DB::table('languages')->get();
    
    
            if($languages->count() > 0){
                foreach ($languages as $language){
                    $data = $this->open_mobile_JSONFile($language->code);
                    if (isset($data[$request->pk])){
                        $data[$request->value] = $data[$request->pk];
                        unset($data[$request->pk]);
                        $this->save_mobile_JSONFile($language->code, $data);
                    }
                }
            }
    
    
            return response()->json(['success'=>'Done!']);
        }

        public function m_destroy($key)
        {
            $languages = DB::table('languages')->get();


            if($languages->count() > 0){
                foreach ($languages as $language){
                    $data = $this->open_mobile_JSONFile($language->code);
                    unset($data[$key]);
                    $this->save_mobile_JSONFile($language->code, $data);
                }
            }
            return response()->json(['success' => $key]);
        }
    // end Moblie JSON
}



