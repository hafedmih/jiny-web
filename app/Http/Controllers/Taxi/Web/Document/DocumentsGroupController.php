<?php

namespace App\Http\Controllers\Taxi\Web\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\DocumentsGroup;
use App\Models\taxi\DriverDocument;
use App\Http\Requests\Taxi\Web\DocumentSaveRequest;

class DocumentsGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:new-document', ['only' => ['documentsSave']]);
        $this->middleware('permission:edit-document', ['only' => ['documentsEdit','documentsUpdate']]);
        $this->middleware('permission:delete-document', ['only' => ['documentsDelete']]);
        $this->middleware('permission:active-document', ['only' => ['documentsActive']]);
    }

    public function index(Request $request)
    {
      
        $documents = DocumentsGroup::get();

        return view('taxi.documents.DocumentsGroup',['documents' => $documents]);
    }

    public function documentsSave(DocumentSaveRequest $request)
    {
        
        $data = $request->all();
              
        foreach ($data['document'] as $key => $value) {

            if(is_null($value)){

                $this->validate($request,[
                    'name'=>'required',
                 ]);

                return back();
            }

            else 
            {
                $insert = DocumentsGroup::create([
                    'name' => $value,
                    'status' => 1
                ]);
            }
            
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function documentsEdit($id)
    {
       
        $data = DocumentsGroup::where('slug',$id)->first();

        return response()->json(['message' =>'success','data' => $data], 200);
    }

    public function documentsUpdate(DocumentSaveRequest $request)
    {
        $data = $request->all();
        foreach ($data['document'] as $key => $value) {
            if(is_null($value)){
                
                $this->validate($request,[
                    'name'=>'required',
                 ]);

                return back();
            }
            else{

                $insert = DocumentsGroup::where('slug',$data['document_slug'])->update([
                    'name' => $value,
                ]);
            }
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function documentsDelete($id)
    {
        $data = DocumentsGroup::where('slug',$id)->first();

        $data = DocumentsGroup::where('slug',$id)->delete();
        
        return back();
    }

    public function documentsActive($id)
    {
        $data = DocumentsGroup::where('slug',$id)->first();

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
