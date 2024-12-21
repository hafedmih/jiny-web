<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\RequestRating;
use App\Models\taxi\RequestQuestions;
use App\Models\taxi\InvoiceQuestions;
use App\Models\taxi\Requests\Request as RequestModel;
use DB;
use App\Models\User;
use File;
use Validator;

class RequestRatingController extends BaseController
{
    public function RequestList(Request $request)
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
            
            $RequestList = RequestRating::where('user_id',$user->id)->get();
            if(is_null($RequestList)){
                return $this->sendError('No Data Found',[],404);  
            }
            else{
                $data['RequestList'] = $RequestList;
                return $this->sendResponse('Data Found',$data,200);  
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'request_id' => 'required',    
            'rating' => 'required',
        ]);

        // dd($request->question_id);

        if($validator->fails()){
            return response()->json(['data' => $validator->errors(),'error'=>'true'], 412);
        }

        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);

            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            // dd($user->id);
            if($user->active == false)

                return $this->sendError('User is blocked so please contact admin',[],403);

                if($user->hasRole('user'))
                {
                    $RequestUserRating = RequestModel::where('id',$request['request_id'])->first();
                           if($request['rating'] == 0.0){
                      $RequestUserRating['driver_rated'] = 1;
                    }else {
                      $RequestUserRating['driver_rated'] = $request['rating'];
                    }
                   $RequestUserRating->update();

                    $request_question = json_decode($request->question_id,true);

                    foreach($request_question as $key => $value) {
                        $check_already_exists = RequestQuestions::where('request_id',$request['request_id'])->where('question_id',$value['id'])->first();
                        if($check_already_exists){
                            $check_already_exists['answer'] = $value['answer'];
                            $check_already_exists->save();
                        }
                        else {
                              $request_driver_rating = RequestModel::where('id',$request['request_id'])->first();

                                $requestquestion = new RequestQuestions();
                                $requestquestion->request_id = $request['request_id'];
                                $requestquestion->question_id = $value['id'];
                                $requestquestion->answer = $value['answer'];
                                $requestquestion->user_id = $user->id;
                                $requestquestion->driver_id = $request_driver_rating->driver_id;
                                $requestquestion->status = 1;
                                $requestquestion->save();

                                

                                
    
                         }
                    }
                   

                }

                else if($user->hasRole('driver')) 
                {
                    $RequestDriverRating = RequestModel::where('id',$request['request_id'])->first();
                     if($request['rating'] == 0.0){
                      $RequestDriverRating['user_rated'] = 1;
                    }else {
                      $RequestDriverRating['user_rated'] = $request['rating'];
                    }
                    $RequestDriverRating->update();

                }

                    $requestrating = new RequestRating();
                    $requestrating->request_id = $request['request_id'];
                    $requestrating->rating = $request['rating'];
                    $requestrating->feedback = $request['feedback'];
                    $requestrating->user_id = $user->id;
                    $requestrating->save();
                

                $rating_avg = RequestRating::where('user_id',$user->id)->avg('rating');

                if($rating_avg == null)
                {
                    $driverrating = User::where('id',$user->id)->first();
                    $driverrating['rating'] = 0;
                }
                else 
                {
                $driverrating = User::where('id',$user->id)->first();
                $driverrating['rating'] = $rating_avg;
                $driverrating->update();
                }

                $response['RequestRating'] = $requestrating;

                DB::commit();
                return $this->sendResponse('Data Found',$response,200); 


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' =>'failure.'.$e,'error'=>'true'], 400); 
            }

    }

}
