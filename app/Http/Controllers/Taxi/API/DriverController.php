<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\boilerplate\OauthClients;
use App\Models\taxi\Driver;
use App\Models\taxi\DumpCompany;
use App\Models\User;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Zone;
use App\Models\taxi\Referral;
use App\Models\boilerplate\Country;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Traits\RandomHelper;
use App\Models\taxi\UserOtp;
use Kreait\Firebase\Factory;
use App\Models\taxi\Documents;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Settings;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Wallet;
use App\Models\taxi\DriverLogs;
use App\Models\taxi\VehicleModel;
use App\Http\Requests\Taxi\API\DriverDocumentUploadRequest;
use Illuminate\Support\Facades\Storage;
use DB;
use File;
use Validator;
use Carbon\Carbon;
use App\Models\taxi\RequestRating;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class DriverController extends BaseController
{
    use RandomHelper;

    public function driversignup(Request $request)
    {

        try{
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'phone_number' => 'required|numeric',
                'country_code' => 'required',
                'vehicle_type_slug' => 'required',
                'device_info_hash' => 'required',
                'device_type' => 'required', // ANDROID/IOS/WEB
                'car_number' => 'required',
                'is_primary' => 'required|boolean',
                'service_location'=>'required',
                'login_method'=> 'required', //COMPANY,INDIVIDUAL
                'service_category' => 'required',//LOCAL/OUTSTATION/RENTAL
               // 'brand_label' =>'required', //YES/NO
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);
            }

            $driver = $request->all();

            $type = Vehicle::where('status',1)->where('slug',$driver['vehicle_type_slug'])->first();
          // dd($type);
            if(is_null($type)){
                return $this->sendError('Invalide type'.$driver['vehicle_type_slug'],[],403);
            }
            // dd();
            $countryCheck = $this->checkValidCountryCode($request->country_code);
            if($countryCheck == false)
            {
                return $this->sendError('Wrong Country Code',[],404);
            }
            //check the credentials are match in the Db
            $userPh = User::where('phone_number',$request->phone_number)->role('driver')->first();

            if(!is_null($userPh)){
                return $this->sendError('User completed the registration process. So please login to continue !!',[],403);
            }

             $userDt = User::where('device_info_hash',$request->device_info_hash)->role('driver')->first();
             if(!is_null($userDt)){
                // if($request->is_primary == true){
                     $userDt->device_info_hash = $request->device_info_hash;
                    $userDt->update();
                // }else{
                //     $data['error_code'] = 1001;
                //     return $this->sendError('Device is already used by some other user. Do you want to continue here ??? ',$data,403);
                // }
             }

            $servicelocation = Zone::where('status',1)->where('slug',$driver['service_location'])->first();
            if(is_null($servicelocation)){
                return $this->sendError('Invalide Service Location',[],404);
            }

            do{
                $ref = "REF-".$this->RandomString(6);
              } while (User::where('referral_code', '=', $ref)->role('driver')->exists());

            //   dd($driver['country_code']);
            $newuser  = new User();
            $newuser->firstname = $request->firstname;
            $newuser->lastname = $request->lastname;
            $newuser->country_code = $request->country_code;
            $newuser->email = $request->email;
           // $newuser->email = $request->phone_number."@lahagni.com";
            //$newuser->password = Hash::make("1522#".$request->phone_numbe);
            //$request->phone_number."@lahagni.com";

            $newuser->phone_number = $request->phone_number;
            $newuser->device_info_hash = $request->device_info_hash;
            $newuser->mobile_application_type = $request->device_type;
            $newuser->referral_code = $ref;
            $newuser->active = 0;
            $newuser->user_referral_code = $request->referral_code;
            $newuser->block_reson = "Admin Blocked";
            $newuser->save();

            $user_refernce_amount = Settings::where('name','wallet_driver_refernce_amount')->first();
            $referan_amount_trip_count = Settings::where('name','referan_amount_trip_count')->first();
            $referan_amount_trip_count = $referan_amount_trip_count ? $referan_amount_trip_count->value : 0;

            $refer_user = User::where('referral_code',$request->referral_code)->first();
            //$driver1 = Driver::where('user_id',$refer_user->id)->first();



            if($refer_user){
                $request_count = RequestModel::where('driver_id',$refer_user->id)->where('is_completed',1)->count();

                if($request_count >= $referan_amount_trip_count){
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
                    Driver::where('user_id',$refer_user->id)->update(['refernce_count' => 0]);
                }
                else{
                    Driver::where('user_id',$refer_user->id)->increment('refernce_count');
                }
            }

            $company_id = 0;
            //check whether the company data is found or not
            if($request->has('company_slug')){
                $company_user = User::role(['Company'])->where('slug',$request->company_slug)->first();
                if(is_null($company_user)){
                    return $this->sendError('Invalide company Name',[],404);
                }else{
                    $company_id = $company_user->id;
                }
            }else{
                //add new data in the temp company table
                $newCompanydetails = new DumpCompany();
                $newCompanydetails->user_id = $newuser->id;
                $newCompanydetails->company_name = $request->company_name;
                $newCompanydetails->company_phone_number = $request->company_phone;
                $newCompanydetails->total_no_of_vehicle = $request->company_no_of_vehicles;
                $newCompanydetails->status = 1;
                $newCompanydetails->save();
            }


            $newuser->assignRole('driver');
            $client = new OauthClients();
            $client->user_id = $newuser->id;
            $client->name =  $newuser->firstname;
            $client->secret = $this->generateRandomString(40);
            $client->redirect = 'http://localhost';
            $client->personal_access_client = false;
            $client->password_client = false;
            $client->revoked = false;
            $client->save();

            $data['client_id'] = $client->id;
            $data['client_secret'] = $client->secret;

            // vehicle Model Number
            $vechile_model = '';
            if($request->has('vehicle_model_slug')){
                $fetchVechileModel = VehicleModel::where('slug',$request->vehicle_model_slug)->first();
                if(is_null($fetchVechileModel))
                    return $this->sendError('Invalide Vechile model',[],404);
                else
                    $vechile_model = $fetchVechileModel->model_name;
            }else{
                $vechile_model = $request->vehicle_model_name;
            }

              $pincode= str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $driverSave = Driver::create([
                'type' => $type->id,
                'car_number' => $driver['car_number'],
                'login_method' => $driver['login_method'],
                'company_id' => $company_id,
                'service_location' => $servicelocation['id'],
                'user_id' => $newuser->id,
                'car_model' => $vechile_model,
                'service_category' => $request->service_category,
                'pincode' =>$pincode,
                //'brand_label' => $request->brand_label,
                'status' => 1
            ]);

            //dd($driverSave);
            if($request->referral_code != '')
            {
                $ref_by = User::where('referral_code', '=', $request->referral_code)->role('driver')->first();
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
            }
            DB::commit();
            return $this->sendResponse($pincode.'#Driver register successfully...',$data,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }
    }
 public function driverLogin(Request $request)
{
    try {
        // Check the validation first
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'country_code' => 'required',
            'device_info_hash' => 'required',
            'is_primary' => 'required',
            'password' => 'required' // Ensure password is provided
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 412);
        }

        // Check whether the country code is valid or not
        $countryCheck = $this->checkValidCountryCode($request->country_code);
        if (!$countryCheck) {
            // Return error if country code is invalid
            return $this->sendError('Wrong Country Code', [], 404);
        }

        // Check if the user exists with the provided phone number and is a driver
        $user = User::where('phone_number', $request->phone_number)->role('driver')->first();

        if (is_null($user)) {
            // If user not found, return error
            return $this->sendError('User not found', [], 404);
        }

        // Retrieve the driver associated with the user
        $driver = Driver::where('user_id', $user->id)->first();

        // Check if pincode matches with the provided password
        if ($driver && $request->password == $driver->pincode) {
            // Update user's device info
            $user->device_info_hash = $request->device_info_hash;
            $user->mobile_application_type = $request->device_type;
            $user->update();

            // Fetch OAuth client details for the user
            $fetchOauth = OauthClients::where('user_id', $user->id)->first();
            if (is_null($fetchOauth)) {
                return $this->sendError('No OAuth client found', [], 403);
            }

            // Return OAuth client details
            $data['client_id'] = $fetchOauth->id;
            $data['client_secret'] = $fetchOauth->secret;
            $data['new_user'] = false;
            return $this->sendResponse('Data Found', $data, 200);
        } else {
            // If pincode does not match, return error
            return $this->sendError('Invalid credentials', [], 401);
        }

    } catch (\Exception $e) {
        // Catch any exceptions and return error
        return $this->sendError('Catch error', 'failure.' . $e, 400);
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

    public function viewUser()
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

            if(!$user->hasRole('driver'))
                return $this->sendError('No User  found',[],403);

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
            $data['user']['car_details'] = $this->getDriverDetails($user->id);

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

            if(!$user->hasRole('driver'))
                return $this->sendError('No User  found',[],403);

            if( $request->has('phone_number') )
            {
                if (env('FIREBASE_CREDENTIALS') && env('FIREBASE_DATABASE_URL')) {
                    $credentials =  public_path(env('FIREBASE_CREDENTIALS'));
                    $firebase = (new Factory)->withServiceAccount($credentials)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
                    $database = $firebase->createDatabase();
                    $getData = $database->getReference('verification/driver/'.$request->phone_number)->getValue();
                    // dd($getData);
                    // die();
                    if(is_null($getData)){
                        return $this->sendError('Unauthorized user',[],401);
                    }
                    if($getData['otp'] != $request->otp)
                    {
                        return $this->sendError('Unauthorized user',[],401);
                    }
                    else
                    {
                        $database->getReference('verification/driver/'.$request->phone_number)->remove();
                    }
                }
                $user->update([
                    'phone_number'  => $request['phone_number'],
                ]);
            }

            /* Profile Picture Uploaded Using Helper here we go */

            if( $request->hasFile('profile_pic') )
            {
                $filename =  uploadImage('images/profile',$request->file('profile_pic'),$user->getRawOriginal('profile_pic'));

            // $filename = time().'.'.$request->profile_pic->extension();
            // $path = Storage::disk('s3')->put('images/profile', $request->profile_pic);
            // $paths = Storage::disk('s3')->put('', $request->profile_pic);

               $user->update([
                    'profile_pic'  => $filename,
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
            $data['user']['car_details'] = $this->getDriverDetails($user->id);

            DB::commit();
            return $this->sendResponse('Data Found',$data,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }

    }

    private function getDriverDetails($user_id){
    // fetch the driver details
        $driverDetails = Driver::with(['zone','vehicletype'])->where('user_id',$user_id)->first();
        if(!is_null($driverDetails)){
            $data['car_number'] = $driverDetails->car_number;
            $data['car_model'] = $driverDetails->car_model;
            $data['car_year'] = $driverDetails->car_year;
            $data['car_colour'] = $driverDetails->car_colour;
            $data['zone_name'] = $driverDetails->zone->zone_name;
            $data['vehicle_type'] = $driverDetails->vehicletype->vehicle_name;
            $data['vehicle_image'] = $driverDetails->vehicletype->image;


            return $data;
        }else{
            $data['car_number'] = '';
            $data['car_model'] = '';
            $data['car_year'] = '';
            $data['car_colour'] = '';
            $data['zone_name'] = '';
            $data['vehicle_type'] = '';
            return $data;
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

            $phone_exists = User::where('phone_number', '=', $request->phone_number)->role('driver')->first();

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

    public function updateDriverDocument(DriverDocumentUploadRequest $request)
    {
        // dd($request['identifier']);
        try{
            $clientlogin = $this::getCurrentClient(request());
            if(is_null($clientlogin))
                return $this->sendError('Token Expired',[],401);

            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);

            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);

            $data = $request->all();

            $document = Documents::where('slug',$data['document_id'])->first();

            if(is_null($document))
                return $this->sendError('Invalid document',[],401);

            if($document->expiry_date == 1 && !$request->has('expiry_date') || $document->expiry_date == 1 && $request['expiry_date'] == "")
                return $this->sendError('Expiry date is required',[],422);

            if($document->expiry_date == 2 && !$request->has('issue_date') || $document->expiry_date == 2 && $request['issue_date'] == "")
                return $this->sendError('Issue date is required',[],422);

                $filename =  uploadImage('images/document',$request->file('document_image'));

                // $filename = time().'.'.$request->document_image->extension();
                // $path = Storage::disk('s3')->put('images/document', $request->document_image);
                // $paths = Storage::disk('s3')->put('', $request->document_image);

                $driverDocument = DriverDocument::with('document')->where('user_id',$user->id)->where('document_id',$document->id)->first();
            if($driverDocument){
                if($request->file('document_image')){
                    deleteImage('images/document',$driverDocument->document_image);
                }
                $driverDocument->document_image = $filename;
                $driverDocument->expiry_date = $request->has('expiry_date') ? $request['expiry_date'] : '';
                $driverDocument->issue_date = $request->has('issue_date') ? $request['issue_date'] : '';
                $driverDocument->exprienc_status = 0;
                $driverDocument->exprience_reson = "";
                $driverDocument->identifier = $request['identifier'];
                $driverDocument->save();
            }
            else{
                $driverDocument = DriverDocument::create([
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'document_image' => $filename,
                    'expiry_date' => $request->has('expiry_date') ? $request['expiry_date'] : '',
                    'issue_date' => $request->has('issue_date') ? $request['issue_date'] : '',
                    'document_status' => 1,
                    'exprienc_status' => 0,
                    'exprience_reson' => "",
                    'identifier' => $request['identifier'],
                    'status' => 1
                ]);
                // dd($driverDocument);
            }

            $documents = Documents::where('requried',1)->where('status',1)->pluck('id')->toArray();
            $driver_documents = DriverDocument::where('user_id',$user->id)->whereIn('document_id',$documents)->count();

            if(count($documents) <= $driver_documents){
                Driver::where('user_id',$user->id)->update(['document_upload_status' => 4]);
            }

            DB::commit();
            return $this->sendResponse('Data uploaded successfully!...',$driverDocument,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }
    }

    public function updateDriverOnline(Request $request)
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

            if($user->online_by == 1){
                $logs = DriverLogs::where('driver_id',$user->id)->where('date',date('Y-m-d'))->WhereNull('offline_time')->orderby('id','desc')->first();
                if($logs){
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $logs->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $logs->offline_time = NOW();
                    $logs->working_time = $login_hours;
                    $logs->save();
                }
                $user->online_by = 0;
            }
            else{
                $logs1 = DriverLogs::where('driver_id',$user->id)->where('date',date('Y-m-d'))->WhereNull('offline_time')->orderby('id','desc')->first();
                if($logs1){
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $logs1->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $logs1->offline_time = NOW();
                    $logs1->working_time = $login_hours;
                    $logs1->save();
                }
                $logs = DriverLogs::create([
                    'driver_id' => $user->id,
                    'date' => date('Y-m-d'),
                    'online_time' => NOW(),
                    'status' => 1
                ]);
                $user->online_by = 1;
            }
            $user->save();

            DB::commit();
            return $this->sendResponse('Driver Status Updated!...',$user,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }
    }
    public function driverlist($param, $query = null)
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

            if($param == 'active'){
                $driver = User::role('driver');
                $driver = $driver->where('active',1)->latest()->with('driver')->search($query)->paginate(10);

                $driverdocum = '';

                foreach ($driver as $key => $value) {

                    $rating = RequestRating::where('user_id',$value->id)->avg('rating');
                    $driver[$key]->rating = (int)$rating;

                    $docum = Documents::where('requried',1)->pluck('id')->toArray();
                    $driver->type = $driver[$key]->driver->vehicletype->vehicle_name;

                    $driverdocum = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status','>=',1)->count();
                    $driverdocum_approved = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status',2)->count();
                    //dd($driverdocum_approved);
                    $message = 'NO';
                    if(count($docum) > $driverdocum){
                        $message = "NO";
                    }
                    if(count($docum) <= $driverdocum){
                        $message = "YES";
                    }

                    if(count($docum) <= $driverdocum_approved){
                        $message = "YES";
                    }

                    if($driverdocum_approved >= 1 )
                    {
                        $message = "YES";
                    }
                    $driver[$key]->documents = $message;

                    $wallet = Wallet::where('user_id',$value->id)->first();
                    if(is_null($wallet))
                        $driver[$key]->wallet_balance = 0;
                    else
                        $driver[$key]->wallet_balance = $wallet->balance_amount;
                }
            }
            else if($param == 'inactive'){
                $driver = User::role('driver');
                $driver = $driver->where('active',0)->where( 'block_reson','Admin Blocked')->latest()->with('driver')->search($query)->paginate(10);
                foreach ($driver as $key => $value) {
                    $rating = RequestRating::where('user_id',$value->id)->avg('rating');
                    $driver[$key]->rating = (int)$rating;
                    $docum = Documents::where('requried',1)->pluck('id')->toArray();
                    $driver->type = $driver[$key]->driver->vehicletype->vehicle_name;
                    //dd($value->id);
                    $driverdocum = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status','>=',1)->count();
                    $driverdocum_approved = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status',2)->count();
                    //dd($driverdocum_approved);
                    $message = 'NO';
                    if(count($docum) > $driverdocum){
                        $message = "NO";
                    }
                    if(count($docum) <= $driverdocum){
                        $message = "YES";
                    }

                    if(count($docum) <= $driverdocum_approved){
                        $message = "YES";
                    }

                    $driver[$key]->documents = $message;
                }
            }else{
                $driver = User::role('driver')->with('driver')->paginate(10);
                foreach ($driver as $key => $value) {
                    $driver->type = $driver[$key]->driver->vehicletype->vehicle_name;
                }
            }
            DB::commit();
            return $this->sendResponse('Driver List!...',$driver,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }
    }

    public function driverstatus(request $request)
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

                $validator = Validator::make($request->all(), [
                'slug' => 'required',
                'status' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error',$validator->errors(),412);
            }

            $driver = User::where('slug', $request->slug)->first();

            if($request->status == $driver->active)
                if($request->status == 1)
                return $this->sendResponse('Driver Aldready Active!...',[],200);
                else
                return $this->sendResponse('Driver Aldready Inactive!...',[],200);
            else if ($request->status == 1) {
                $driver->active = $request->status;
                $driver->block_reson = "";
                $driver->update();
            }
            else{
                $driver->active = $request->status;
                $driver->block_reson = "Admin Blocked";
                $driver->update();
                DB::commit();
            }
            return $this->sendResponse('Driver Status Updated Successfully!...',$driver,200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);
        }
    }
}
