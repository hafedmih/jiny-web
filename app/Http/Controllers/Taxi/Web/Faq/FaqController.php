<?php

namespace App\Http\Controllers\Taxi\Web\Faq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Faq;
use App\Http\Requests\Taxi\Web\FAQSaveRequest;
use App\Models\boilerplate\Languages;
use Carbon\Carbon;
use File;
use DB;


class FaqController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:new-faq', ['only' => ['faqSave']]);
        $this->middleware('permission:edit-faq', ['only' => ['faqEdit','faqUpdate']]);
        $this->middleware('permission:delete-faq', ['only' => ['destroy']]);
        $this->middleware('permission:status-change-faq', ['only' => ['faqChangeStatus']]);
    }

     public function faq(Request $request)
    {
        $languages = Languages::where('status',1)->get();
        $columns = [];
      
       if($languages->count() > 0){
           foreach ($languages as $key => $language){
               $columns[$language->code] = $this->openJSONFile($language->code);
           }
       }
        $column = json_decode(json_encode($columns),true);
  
        return view('taxi.faq-management.faq-management',['column' => $column],['languages' => $languages]);
    }

     public function faqSave(FAQSaveRequest $request)
    {
        $data = $request->all();
    
        $faq = Faq::create([
            'question' => $data['question'],
            'answer' => $data['answer'],
            'category' => $data['category'],
            'language' =>$data['language'],
            'status' =>  $data['status'],
            'slug' => Carbon::now()->timestamp
        ]);
        $data = $this->openJSONFile($request->language);

            $data[$faq->slug] = $faq; 
         
            $this->saveJSONFile($request->language, $data);
        return response()->json(['message' =>'success'], 200);

    }

    public function faqEdit($key)
    {
        $languages = DB::table('languages')->get();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key])){
                    $faq=$data[$key];
                }
            }
        }
        return response()->json(['message' =>'success','faq' => $faq,'key'=>$key], 200);
    }

    public function faqView($id)
    {
        $faq = Faq::where('slug',$id)->first();
        return response()->json(['message' =>'success','faq' => $faq], 200);
    }

    public function faqDelete($id)
    {
        $faq = Faq::where('slug',$id)->delete();
        return redirect()->route('faq-management');
    }


    public function faqUpdate(FAQSaveRequest $request)
    {
        $languages = DB::table('languages')->get();

        $faq = $request->all();
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$request->faq_id])){
                    $f = $data[$request->faq_id];  
                    if($f > 0){
                        $f['answer'] = $faq['answer'];
                        $f['question'] = $faq['question'];
                        $f['category'] = $faq['category'];
                    }
                   $data[$request->faq_id]= $f;
                  $this->saveJSONFile($language->code, $data);
                }
            }
        }

        return response()->json(['message' =>'success'], 200);
    }

    public function faqChangeStatus($key)
    {
        $languages = DB::table('languages')->get();
       
        if($languages->count() > 0){
            foreach ($languages as $language){
                $data = $this->openJSONFile($language->code);
                if (isset($data[$key]))
                {
                   $faq = $data[$key];
                    if($faq['status'] == "1"){
                        $faq['status'] = "0";
                    }
                    else{
                        $faq['status'] = "1";
                    }
                    $data[$key]= $faq;
                    $this->saveJSONFile($language->code, $data);
                } 
            }
        }
        return redirect()->route('faq-management');
    }

    public function faqLanguage(Request $request)
    {
        $data = $request->all();
        
        $faq = Faq::create([
            'question' => $data['question'],
            'answer' => $data['answer'],
            'category' => $data['category'],
            'language' =>$data['language'],
            'status' => 1,
            'slug' => Carbon::now()->timestamp
        ]);

        return response()->json(['message' =>'success'], 200);

    }

    private function openJSONFile($code){
        $jsonString = [];
        if(File::exists(base_path('resources/lang/faq_'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/faq_'.$code.'.json'));
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
        file_put_contents(base_path('resources/lang/faq_'.$code.'.json'), stripslashes($jsonData));
       
       
    }
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
      //  return redirect()->json(['message' =>'success'], 200);
        return redirect()->route('faq-management');
    }
}
