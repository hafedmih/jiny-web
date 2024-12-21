<?php


namespace App\Http\Controllers\Taxi\Web\Faq;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\boilerplate\Languages;
use DB;
use File;


class FaqlanguageController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @return Response
    */
   public function index()
    {
      $languages = Languages::where('status',1)->get();
   	  $columns = [];
	  $columnsCount = $languages->count();
     
	    if($languages->count() > 0){
	        foreach ($languages as $key => $language){
                
	            if ($key == 0) {
	                $columns[$key] = $this->openJSONFile($language->code);
	            }
	            $columns[++$key] = ['data'=>$this->openJSONFile($language->code), 'lang'=>$language->code];
	        }
	    }
      
         //mobile 
         if($languages->count() > 0){
	        foreach ($languages as $key => $language){
	            if ($key == 0) {
	                $column[$key] = $this->openJSONFile('en_mobile');
                    
	            }
	            $column[++$key] = ['data'=>$this->openJSONFile('en_mobile'), 'lang'=>'en_mobile'];
        
	        }
	    }


   	  return view('boilerplate.languages.languages', compact('languages','column','columns','columnsCount'));
    }   
    /**
     * Remove the specified resource from storage.
     * @return Response
    */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
		    'answer' => 'required',
		    'category' => 'required',
            'language' => 'required',
		]);

		$data = $this->openJSONFile($request->language);
        $data[$request->question] = $request->answer;
        $data[$request->category];
        $this->saveJSONFile($request->language, $data);


        return redirect()->route('faq-ma');
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
        if(File::exists(base_path('resources/lang/language/'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/language/'.$code.'.json'));
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
        file_put_contents(base_path('resources/lang/language/'.$code.'.json'), stripslashes($jsonData));
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
}
