<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\taxi\Documents;
use App\Models\taxi\DriverDocument;
use App\Models\User;

use DB;
use File;
use Validator;


/** Document Grouped list */


class DocumentController extends BaseController
{
    public function documentsList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
    
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
        
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);
            
            $documents = DocumentsGroup::where('status',1)->get();

            $data_array = [];
            
            foreach ($documents as $key => $value) {
                $documents[$key]->document_count = count($value->getDocument);
                $upload_count = 0;
                foreach($value->getDocument as $key1 => $value1){
                    $DriverDocuments = DriverDocument::where('user_id',$user->id)->where('document_id',$value->id)->first();
                    if($DriverDocuments){
                        $value->getDocument[$key1]->is_uploaded = 1;
                        $value->getDocument[$key1]->document_image = $DriverDocuments->document_image;
                        $value->getDocument[$key1]->expiry_dated = $DriverDocuments->expiry_date;
                        // $value->getDocument[$key1]->document_status = $DriverDocuments->document_status;
                        $value->getDocument[$key1]->issue_date = $DriverDocuments->issue_date;
                        $value->getDocument[$key1]->exprience_status = $DriverDocuments->exprienc_status;
                        // $value->getDocument[$key1]->exprience_reson = $DriverDocuments->exprience_reson;
                        $value->getDocument[$key1]->identifier_document = $DriverDocuments->identifier;
                        $upload_count++;
                    }
                    else{
                        $value->getDocument[$key1]->document_image = '';
                        $value->getDocument[$key1]->expiry_date = 0;
                        $value->getDocument[$key1]->issue_date = 0;
                        $value->getDocument[$key1]->is_uploaded = 0;
                    }
                }
                if(count($value->getDocument) == $upload_count){
                    $documents[$key]->upload_status = 1;
                }
                $documents[$key]->driver_document = $value->getDocument;
                if(count($value->getDocument) > 0){
                    array_push($data_array, $documents[$key]);
                }
            }
            if(is_null($data_array)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data = array();
                $data['document'] = $data_array;
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
}