<?php

namespace App\Http\Controllers\Taxi\Web\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Documents;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\DocumentsGroup;
use App\Http\Requests\Taxi\Web\DocumentSaveRequest;
use Illuminate\Support\Carbon;
use DateTime;
use DB;

class DocumentController extends Controller
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
        $documents = Documents::get();
        $document_group = DocumentsGroup::where('status',1)->get();

        return view('taxi.documents.documents',['documents' => $documents,'document_group' => $document_group]);
    }

    public function documentsSave(DocumentSaveRequest $request)
    {
        
        $data = $request->all();
              
        foreach ($data['document'] as $key => $value) {

            if(is_null($value)){

                $this->validate($request,[
                    'document_name'=>'required',
                 ]);

                return back();
            }

            else 
            {
                $insert = Documents::create([
                    'document_name' => $value,
                    'requried' => $data['required_value'][$key],
                    'identifier' => $data['identifier_value'][$key],
                    'expiry_date' => $data['experied'][$key],
                    'group_by' => $data['group_by'][$key],
                    'status' => 1
                ]);
            }
            
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function documentsEdit($id)
    {
        $data = Documents::where('slug',$id)->first();

        return response()->json(['message' =>'success','data' => $data], 200);
    }

    public function documentsUpdate(DocumentSaveRequest $request)
    {
        $data = $request->all();
        foreach ($data['document'] as $key => $value) {
            if(is_null($value)){
                
                $this->validate($request,[
                    'document_name'=>'required',
                 ]);

                return back();
            }
            else{

                $insert = Documents::where('slug',$data['document_slug'])->update([
                    'document_name' => $value,
                    'requried' => $data['required_value'][$key],
                    'identifier' => $data['identifier_value'][$key],
                    'expiry_date' => $data['experied'][$key],
                    'group_by' => $data['group_by'][$key]
                ]);
            }
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function documentsDelete($id)
    {
        $data = Documents::where('slug',$id)->first();

        $driverDocument = DriverDocument::where('document_id',$data->id)->count();
        if($driverDocument > 0){
            session()->flash('message',"This Document uploaded. So can not deleted this Document");
            return back();
        }
        $data = Documents::where('slug',$id)->delete();
        
        return back();
    }

    public function documentsActive($id)
    {
        $data = Documents::where('slug',$id)->first();

        if($data->status == 1){
            $data->status = 0;
        }
        else{
            $data->status = 1;
        }
        $data->save();
        
        return back();
    }

    public function documentExpiry(Request $request)
    {
        $now = date('Y-m-d');
        $current_date = new DateTime($now);
        $date = Carbon::now()->addDays(10);
        $document =  DriverDocument::join('users', 'users.id', '=', 'driver_document.user_id')->where('driver_document.status',1)->join('documents', 'documents.id', '=', 'driver_document.document_id')->whereNotNull('driver_document.expiry_date')->where('documents.status',1)->where('documents.expiry_date',1)->whereDate('driver_document.expiry_date','<=',$date)->whereDate('driver_document.expiry_date','>=',date('Y-m-d'))->select('users.firstname','driver_document.expiry_date','documents.document_name','users.slug','users.phone_number','users.lastname')->get();

        $days = $current_date->diff($date)->format("%a");
        foreach ($document as $key => $value) {
            $exp_date = new DateTime($value->expiry_date);
            $days = $current_date->diff($exp_date)->format("%a");
            $document[$key]->days = $days;
        }
        return view('taxi.documents.expiry',['document' => $document]);

    }
}
