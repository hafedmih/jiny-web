<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\API\UserProfileRequest;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\boilerplate\Country;
use DB;
use File;
use Validator;
use Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Kreait\Firebase\Factory;
use App\Traits\RandomHelper;
use App\Models\taxi\Favourite;



class UserController extends BaseController
{

    use RandomHelper;

    public function viewUser()
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
            
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            // dd($user);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);


            $countrydetails = Country::find($user->country_code);
            if(is_null($countrydetails))
                return $this->sendError('No country details found',[],404);
            
        //    if(!$user->hasRole('user'))
        //         return $this->sendError('No User  found',[],403);

            $FavouriteList = Favourite::where('user_id',$user->id)->groupBy('address')->orderBy('created_at', 'DESC')->get();


            $data['user']['slug'] = $user->slug;
            $data['user']['firstname'] = $user->firstname;
            $data['user']['lastname'] = $user->lastname;
            $data['user']['email'] = $user->email;
            $data['user']['phone_number'] = $user->phone_number;
            $data['user']['currency'] = $countrydetails->currency_symbol;
            $data['user']['country'] = $countrydetails->name;
            $data['user']['profile_pic'] = $user->profile_pic;
            $data['FavouriteList'] = $FavouriteList;

            
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function updateProfile(Request $request)
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

            if(!$user->hasRole('user'))
                return $this->sendError('No User  found',[],403);
                
            if( $request->has('phone_number') ) 
            {
                if (env('FIREBASE_CREDENTIALS') && env('FIREBASE_DATABASE_URL')) {
                    $credentials =  public_path(env('FIREBASE_CREDENTIALS'));
                    $firebase = (new Factory)->withServiceAccount($credentials)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
                    $database = $firebase->createDatabase();
                    $getData = $database->getReference('verification/user/'.$request->phone_number)->getValue();
                    if(is_null($getData)){
                        return $this->sendError('Unauthorized user',[],401);
                    }
                    if($getData['otp'] != $request->otp)
                    {
                        return $this->sendError('Unauthorized user',[],401);
                    }
                    else
                    {
                        $database->getReference('verification/user/'.$request->phone_number)->remove();
                    }
                }
                $user->update([
                    'phone_number'  => $request['phone_number'],
                ]);
            }

            /* Profile Picture Uploaded Using Helper here we go */

            if( $request->hasFile('profile_pic') ) 
            {
                //    $filename =  uploadImage('images/profile',$request->file('profile_pic'),$user->getRawOriginal('profile_pic'));

            $filename = time().'.'.$request->profile_pic->extension(); 
            $path = Storage::disk('s3')->put('images/profile', $request->profile_pic);
            $paths = Storage::disk('s3')->put('', $request->profile_pic);
            
               $user->update([
                    'profile_pic'  => $paths,
                ]); 
            }
            else 
            {
                $user->update($request->only(['firstname', 'lastname', 'email']));
            }

            $countrydetails = Country::find($user->country_code);
            if(is_null($countrydetails))
                return $this->sendError('No country details found',[],404);
            

            $data['user']['slug'] = $user->slug;
            $data['user']['firstname'] = $user->firstname;
            $data['user']['lastname'] = $user->lastname;
            $data['user']['email'] = $user->email;
            $data['user']['phone_number'] = $user->phone_number;
            $data['user']['currency'] = $countrydetails->currency_symbol;
            $data['user']['country'] = $countrydetails->name;
            $data['user']['profile_pic'] = $user->profile_pic;

            
            DB::commit();
            
            return $this->sendResponse('Data Found',$data,200);  
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
        
    }


    public function CheckPhoneNumber(Request $request)
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

            $phone_exists = User::where('phone_number', '=', $request->phone_number)->role('user')->first();
            
            if ($phone_exists === null) {
                return $this->sendResponse('Data Found',[],200); 
            } else 
            {
                return $this->sendError('Phone Number Already Exists',[],403);
            }        
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function userlanguage(Request $request)
   

    {  
        try{
            $validator = Validator::make($request->all(), [
                'language' => 'required'
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);       
            }

            $clientlogin = $this::getCurrentClient(request());
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
        
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

           
            

            $user = User::where('id',$user->id)->update([
                'language' => $request->language,
                
            ]);

            return $this->sendResponse('User language Added Successfully!...',$user,200);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); 
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    

   
}

   

