<?php
namespace App\Http\Controllers\boilerplate\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\taxi\UserOtp;
use App\Models\taxi\Referral;
use App\Models\boilerplate\OauthClients;
use App\Models\boilerplate\Country;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Settings;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Auth;
use App\Traits\RandomHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthAPIController extends BaseController
{
   
    use RandomHelper;

    public function __construct(){
    }

    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'country_code' => 'required',
                'email' => 'sometimes|email',
                'firstname' => 'required',
                // 'lastname' => 'required',
                'device_info_hash' => 'required',
                'device_type' => 'required', // ANDROID/IOS/WEB
                'is_primary' => 'required|boolean'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),412);       
            }
    
            // Check whether the country code is valid or not
            $countryCheck = $this->checkValidCountryCode($request->country_code);
            if($countryCheck == false)
            {
                return $this->sendError('Wrong Country Code',[],404);
            }

            //check the credentials are match in the Db
            $userPh = User::where('phone_number',$request->phone_number)->role('user')->first();
            if(!is_null($userPh)){
                return $this->sendError('User completed the registration process. So please login to continue !!',[],403);
            }

             $userDt = User::where('device_info_hash',$request->device_info_hash)->role('user')->first();
             if(!is_null($userDt)){
                //if($request->is_primary == true){
                    $userDt->device_info_hash = $request->device_info_hash;
                    $userDt->update();
                // }else{
                //     $data['error_code'] = 1001;
                //     return $this->sendError('Device is already used by some other user. Do you want to continue here ??? ',$data,403);
                // }
             }
            
            do{
                $ref = "REF-".$this->RandomString(6);
              } while (User::where('referral_code', '=', $ref)->exists());


            $newuser  = new User();
            $newuser->firstname = $request->firstname;
            $newuser->lastname = $request->lastname != "" ? $request->lastname : '';
            $newuser->country_code = $request->country_code;
            $newuser->email = $request->email;
            $newuser->phone_number = $request->phone_number;
            $newuser->device_info_hash = $request->device_info_hash;
            $newuser->mobile_application_type = $request->device_type;
            $newuser->referral_code = $ref;
            $newuser->user_referral_code = $request->referral_code;
            $newuser->active = true;
            if($request->has('profile_pic') && $request->profile_pic != ""){
                $filename =  uploadImage('images/profile',$request->file('profile_pic'));
                $newuser->profile_pic = $filename;
            }
            $newuser->save();
            if($request->referral_code !='')
            {
                $ref_by = User::where('referral_code', '=', $request->referral_code)->first();
                if($ref_by!=null)
                {
                    $referralby = new Referral();
                    $referralby->referred_by = $ref_by->id;
                    $referralby->user_id = $newuser->id;
                    $referralby->save();
                }
                else 
                {
                    return $this->sendError('Invalid Referral Code',[],403); 
                }

                $user_refernce_amount = Settings::where('name','wallet_user_refernce_amount')->first();

                $refer_user = User::where('referral_code',$request->referral_code)->first();

                if($refer_user){
                    $request_count = 
                    $wallet = Wallet::where('user_id',$refer_user->id)->first();
                    if($wallet){
                        $wallet->earned_amount += $user_refernce_amount ? $user_refernce_amount->value : 0;
                        $wallet->balance_amount += $user_refernce_amount ? $user_refernce_amount->value : 0;
                    }
                    else{
                        $wallet = Wallet::create([
                            'user_id' => $refer_user->id,
                            'earned_amount' => $user_refernce_amount ? $user_refernce_amount->value : 0,
                            'balance_amount' => $user_refernce_amount ? $user_refernce_amount->value : 0,
                            'amount_spent' => 0
                        ]);
                    }

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'amount' => $user_refernce_amount ? $user_refernce_amount->value : 0,
                        'purpose' => "Refernce Amount",
                        'type' => "EARNED",
                        'user_id' => $refer_user->id
                    ]);
                }
            }
           
           
            $newuser->assignRole('user');

            $client = new OauthClients();
            $client->user_id = $newuser->id;
            $client->name =  $request->firstname;
            $client->secret = $this->generateRandomString(40);
            $client->redirect = 'http://localhost';
            $client->personal_access_client = false;
            $client->password_client = false;
            $client->revoked = false;
            $client->save();

            $data['client_id'] = $client->id;
            $data['client_secret'] = $client->secret;
            DB::commit();
            return $this->sendResponse('Data Found',$data,200);
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
   
    /**
     * Login api
     * Check the validation First
     * Check whether the country code is valid or not
     * check the credentials are match in the Db
     * Check Device Token 
     * fetch the oauth credentials
     * send response
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try{
            //Check the validation First
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'country_code' => 'required',
                'otp' => 'required',
                'device_info_hash' => 'required',
                'is_primary' => 'required'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),412);       
            }


            // Check whether the country code is valid or not
            $countryCheck = $this->checkValidCountryCode($request->country_code);
            if($countryCheck == false)
            {
                return $this->sendError('Wrong Country Code',[],404);
            }

               // if FirebaseOTP 
            //    if(1 != 1){
            //     if (env('FIREBASE_CREDENTIALS') && env('FIREBASE_DATABASE_URL')) {
                
            //         $credentials =  public_path(env('FIREBASE_CREDENTIALS'));
            //         $firebase = (new Factory)->withServiceAccount($credentials)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
            //         $database = $firebase->createDatabase();
            //         $getData = $database->getReference('verification/user/'.$request->phone_number)->getValue();
            //         if(is_null($getData)){
            //             return $this->sendError('Unauthorized user',[],401);
            //         }
            //         if($getData['otp'] != $request->otp)
            //         {
            //             return $this->sendError('Unauthorized user',[],401);
            //         }
            //         else
            //         {
            //             $database->getReference('verification/user/'.$request->phone_number)->remove();
            //         }
            //     }
            // }else{
                $sendedOtp = UserOtp::where('phone_number',$request->phone_number)->where('country_code',$request->country_code)->where('otp',$request->otp)->first();
                // dd($sendedOtp);
                if(is_null($sendedOtp)){
                    return $this->sendError('Wrong Otp',[],401);
                }
                
            // } 
            // Check Data In Firebase
             //check the credentials are match in the Db
            $user = User::where('phone_number',$request->phone_number)->where('active',true)->role('user')->first();
            if(is_null($user)){
                $data['new_user'] = true;
                return $this->sendResponse('Data Found',$data,200); 
            }

            // check the device token  
            // if($user->device_info_hash != $request->device_info_hash){
                // if($request->is_primary == true){
                    $user->device_info_hash = $request->device_info_hash;
                    $user->update();
                // }else{
                //     $data['error_code'] = 1001;
                //     return $this->sendError('User is already logged in some other device. Do you want to continue here ??? ',$data,403);
                // }
            // }
         
            

            // fetch the oauth credentials
            $fetchOauth = OauthClients::where('user_id',$user->id)->first();
            if(is_null($fetchOauth)){
                return $this->sendError('No user Found',[],403);
            }
            
            $data['client_id'] = $fetchOauth->id;
            $data['client_secret'] = $fetchOauth->secret;
            $data['new_user'] = false;
            $sendedOtp->delete();
            DB::commit();
            return $this->sendResponse('Data Found',$data,200); 
            
           
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function loginwithUsernamePassword(Request $request)
    {
        try{
            //Check the validation First
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),412);       
            }

            $user = User::where('email',$request->email)->where('active',true)->first();
            if(is_null($user)){
                $data['new_user'] = true;
                return $this->sendResponse('Data Found',$data,200); 
            }
            $data1 = Hash::check(request('password'), $user->password);
            if($data1){
                $fetchOauth = OauthClients::where('user_id',$user->id)->first();
                if(is_null($fetchOauth)){
                    return $this->sendError('No user Found',[],403);
                }
                // dd($fetchOauth);
                $data['client_id'] = $fetchOauth->id;
                $data['client_secret'] = $fetchOauth->secret;
                $data['new_user'] = false;
                DB::commit();
                return $this->sendResponse('Data Found',$data,200); 
            }else{
                return $this->sendError('Wrong Credentials', [],403);
            }
            // fetch the oauth credentials
            
            
           
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    private function checkValidCountryCode($code)
    {
        $country = Country::where('id',$code)->where('status',1)->first();
        if(is_null($country))
        {
            return false;
        }else
        {
            return true;
        }
    }

    public function sendOtp(Request $request){
        try{
            //Check the validation First
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'country_code' => 'required',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),412);       
            }

            // Check whether the country code is valid or not
            $countryCheck = $this->checkValidCountryCode($request->country_code);
            if($countryCheck == false)
            {
                return $this->sendError('Wrong Country Code',[],404);
            }
            //$otp = $this->UniqueRandomNumbers(4);
            $otp = '1234';
           
            //Creative Point 

            //$data = Http::get('http://smsserver9.creativepoint.in/api.php?username=cpdemo&password=cpdemo@123&to='.$request->phone_number.'&from=CRTADS&message=Dear Friend your OTP is '.$otp.' Sent By CRTADS	&PEID=1501401700000018428&templateid=1507162097922951568&PEID=1501401700000018428&templateid=1507162097922951568');

            //Mydream
            if($request->phone_number == '9090909090'){
            
                $user1 = new UserOtp();
                $user1->otp = '1234';
                $user1->phone_number = $request->phone_number;
                $user1->country_code = $request->country_code;
                $user1->save();
            }else{
                // $data = Http::get('http://app.mydreamstechnology.in/vb/apikey.php?apikey=Adbhkho7qOd50OHK&senderid=NPTECH&number='.$request->phone_number.'&message=Dear Friend, Use code '.$otp.' to log in to your NPTECH. Never share your OTP with anyone.');
 
                // $post = json_decode($data->getBody()->getContents());
                
                
                $user1 = new UserOtp();
                $user1->otp = $otp;
                $user1->phone_number = $request->phone_number;
                $user1->country_code = $request->country_code;
                $user1->save();
            }
            

            return $this->sendResponse('Success',[],200);
           
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }
    public function logout (Request $request) {
      
        $clientlogin = $this::getCurrentClient(request());
        if(is_null($clientlogin)) 
            return $this->sendError('Token Expired',[],401);
        
        $user = User::find($clientlogin->user_id);
        if(is_null($user))
            return $this->sendError('Unauthorized',[],401);
        
        if($user->online_by == 1){
            $user->online_by = 0;
            $user->save();
           
        } 
        auth()->logout();
        // JWTAuth::invalidate($request->header('Authorization'));
        
        
        return $this->sendResponse('Success',[],200); 
       
    }


}