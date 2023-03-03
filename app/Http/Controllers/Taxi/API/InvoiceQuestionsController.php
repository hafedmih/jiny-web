<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\InvoiceQuestions;
use DB;
use App\Models\User;
use File;
use Validator;

class InvoiceQuestionsController extends BaseController
{
    public function InvoiceQuestionsList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);
                
            $invoice_questions_list = InvoiceQuestions::where('status',1)->limit(5)->get();

                $data['invoice_questions_list'] = $invoice_questions_list;
                
                return $this->sendResponse('Data Found',$data,200);  

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

}
