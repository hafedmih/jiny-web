<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\taxi\Documents;
use App\Models\taxi\DocumentsGroup;
use App\Models\taxi\DriverDocument;
use App\Models\User;

use DB;
use File;
use Validator;

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
            
            $document = Documents::where('status',1)->get();

            if(is_null($document)){
                return $this->sendError('No Data Found',[],404);  
            }
            // dd($document);
            foreach ($document as $key => $value) {
                $driverDocument = DriverDocument::where('user_id',$user->id)->where('document_id',$value->id)->first();
                $document[$key]->date_required = $document[$key]->expiry_date;
                if($driverDocument){
                    $document[$key]->document_image = $driverDocument->document_image;
                    $document[$key]->expiry_date = $driverDocument->expiry_date;
                    $document[$key]->issue_date = $driverDocument->issue_date;
                    $document[$key]->document_expiry = $driverDocument->exprienc_status;
                    $document[$key]->is_uploaded = 1;
                    $document[$key]->identifier_value = $driverDocument->identifier;
                    
                }
                else{
                    $document[$key]->document_image = '';
                    $document[$key]->expiry_date = 0;
                    $document[$key]->issue_date = 0;
                    $document[$key]->is_uploaded = 0;
                    
                }
            }
            $driver_document = DriverDocument::join('documents','documents.id','=','driver_document.document_id')->where('driver_document.user_id',$user->id)->where('documents.requried',1)->count();

            $required_document = Documents::where('status',1)->where('requried',1)->count();


            
            $data['document'] = $document;
            $data['all_required_documents_uploaded'] = $required_document <= $driver_document ? 1 : 0;
            return $this->sendResponse('Data Found',$data,200);  
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    
    public function documentsgroupList(Request $request)
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
            // dd($user);
            $data_array = [];
            $all_count = 0;
            $uplod_all_count = 0;

            $not_required = 0;
            foreach ($documents as $key => $value) {
                
                $documents[$key]->document_count = count($value->getDocument);
                $total_count = 0;
                $upload_count = 0;
                $not_required = count($value->getDocument);
                $not_required_upload_count = 0;
                foreach($value->getDocument as $key1 => $value1){
                    $DriverDocuments = DriverDocument::where('user_id',$user->id)->where('document_id',$value1->id)->first();
                    // dd($DriverDocuments);
                    if($value1->requried){
                        $total_count++;
                        $all_count++;
                    }
                    // $not_required++;
                   
                    if($DriverDocuments){
                        $value->getDocument[$key1]->is_uploaded = 1;
                        $value->getDocument[$key1]->document_image = $DriverDocuments->document_images;
                        $value->getDocument[$key1]->expiry_dated = $DriverDocuments->expiry_date;
                        if($value->getDocument[$key1]->identifier == 1){
                            $value->getDocument[$key1]->document_number = $DriverDocuments->document_number;
                        }
                        $value->getDocument[$key1]->issue_date = $DriverDocuments->issue_date;
                        $value->getDocument[$key1]->exprience_status = $DriverDocuments->exprienc_status;
                        // $value->getDocument[$key1]->exprience_reson = $DriverDocuments->exprience_reson;
                        $value->getDocument[$key1]->identifier_document = $DriverDocuments->identifier;
                        if($value1->requried){
                            $upload_count++;
                            $uplod_all_count++;
                        }
                        $not_required_upload_count++;
                    }
                    else{
                        $value->getDocument[$key1]->document_image = '';
                        $value->getDocument[$key1]->expiry_dated = 0;
                        $value->getDocument[$key1]->issue_date = 0;
                        $value->getDocument[$key1]->is_uploaded = 0;

                        if ($value->getDocument->has('requried')){
                            if($value->getDocument[$key1]->requried == 0){
                                $upload_count++;
                            }
                        } 
                    }
                    
                    if ($value->getDocument[$key1]->expiry_date == 1){
                        
                        if($value->getDocument[$key1]->expiry_dated != '' && $value->getDocument[$key1]->expiry_dated != '0000-00-00' && $value->getDocument[$key1]->expiry_dated != '0'){
                            if($value->getDocument[$key1]->expiry_dated <  date('Y-m-d')){
                                $documents[$key]->expired_status = 1;
                            }
                        }
                    }
                }
                
                if($total_count == $upload_count && $upload_count > 0){
                    $documents[$key]->upload_status = 1;
                }else{
                    $documents[$key]->upload_status = 0;
                }

                // dump($not_required);
                // dump($not_required_upload_count);

                if($not_required == $not_required_upload_count){
                    $documents[$key]->upload_status = 1;
                }
               // else{
                 //   $documents[$key]->upload_status = 0;
               // }

                

                if($not_required == $not_required_upload_count){
                    $documents[$key]->upload_status = 1;
                }
                // else{
                //     $documents[$key]->upload_status = 0;
                // }

                


                $documents[$key]->get_document = $value->getDocument;
                if(count($value->getDocument) > 0){
                    array_push($data_array, $documents[$key]);
                }
                   
            }
            
           
            if($all_count == $uplod_all_count && $all_count > 0){
                $upload_status = true;
            }
            else{
                $upload_status = false;
            }

               
            if(is_null($data_array)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data = array();
                $data['document'] = $data_array;
                $data['all_documents_upload'] = $upload_status;
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
        
}
