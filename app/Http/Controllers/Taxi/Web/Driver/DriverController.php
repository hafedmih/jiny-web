<?php

namespace App\Http\Controllers\Taxi\Web\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\DriverSaveRequest;
use App\Http\Requests\Taxi\Web\DocumentUploadRequest;
// use App\Http\Requests\Taxi\Web\DumbCompanySaveRequest;
use App\Jobs\SendPushNotification;
use App\Models\boilerplate\OauthClients;
use App\Models\User;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Driver;
use App\Models\taxi\DriverDocument;
use App\Models\boilerplate\Country;
use App\Models\taxi\Documents;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Fine;
use App\Models\taxi\UserComplaint;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Settings;
use App\Models\taxi\Submaster;
use App\Models\taxi\DriverSubscriptions;
use App\Models\taxi\DriverLogs;
use App\Models\taxi\DumpCompany;
use App\Models\taxi\VehicleModel;
use App\Models\taxi\RequestQuestions;
use App\Models\taxi\InvoiceQuestions;
use App\Models\taxi\Requests\Request as RequestModels;
use App\Models\taxi\ReferalAmountList;
use App\Traits\CommanFunctions;
use Illuminate\Support\Facades\Storage;
use App\Traits\RandomHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\taxi\DocumentsGroup;
use App\Models\taxi\GoHome;
use App\Models\taxi\Requests\Request as RequestModel;



use DB;
use Carbon\Carbon;
use App\Constants\PushEnum;
use DateTime;

class DriverController extends Controller
{
    use CommanFunctions;
    use RandomHelper;    
    public function driver(Request $request)
    {
        $drivers_active = User::role('driver')->with(['getCountry']);
        $drivers_incative = User::role('driver')->with(['getCountry']);
        $drivers_block = User::role('driver')->with(['getCountry']);
        // $drivers = Driver::get();
       
        $onlinecount = User::role('driver');
        $offlinecount = User::role('driver');

        if(auth()->user()->hasRole('Company')){
            $drivers_active = $drivers_active->join('drivers','users.id','=','drivers.user_id')->select('users.*')->where('drivers.company_id',auth()->user()->id);
            $drivers_incative = $drivers_incative->join('drivers','users.id','=','drivers.user_id')->select('users.*')->where('drivers.company_id',auth()->user()->id);
            $drivers_block = $drivers_block->join('drivers','users.id','=','drivers.user_id')->select('users.*')->where('drivers.company_id',auth()->user()->id);

            $onlinecount = $onlinecount->join('drivers','users.id','=','drivers.user_id')->select('users.*')->where('drivers.company_id',auth()->user()->id);
            $offlinecount = $offlinecount->join('drivers','users.id','=','drivers.user_id')->select('users.*')->where('drivers.company_id',auth()->user()->id);
        }

        $drivers_active = $drivers_active->where('active',1)->latest()->get();
        $drivers_incative = $drivers_incative->where('active',0)->where( 'block_reson','Admin Blocked')->latest()->get();
        $drivers_block = $drivers_block->where('active',0)->where( 'block_reson','!=','Admin Blocked')->latest()->get();
        $onlinecount = $onlinecount->where('active',1)->where('online_by',1)->get()->count();
        $offlinecount = $offlinecount->where('active',1)->where('online_by',0)->get()->count();

        $count = array();
        $active_count =array();
        $block_count =array();
        $types = Vehicle::where('status',1)->pluck('vehicle_name','id')->toArray();
        foreach($types as $key => $value){
            $drivings = Driver::where('type',$key)->join('users','drivers.user_id','=','users.id')->where('users.active',0);
            if(auth()->user()->hasRole('Company')){
                $drivings = $drivings->where('drivers.company_id',auth()->user()->id);
            }
            $drivings = $drivings->count();
            array_push($count,$drivings);
            
            $drivings_active = Driver::where('type',$key)->join('users','drivers.user_id','=','users.id')->where('users.active',1);
            if(auth()->user()->hasRole('Company')){
                $drivings_active = $drivings_active->where('drivers.company_id',auth()->user()->id);
            }
            $drivings_active = $drivings_active->count();
            array_push($active_count,$drivings_active);

            $drivings_block = Driver::where('type',$key)->join('users','drivers.user_id','=','users.id')->where('active',0)->where( 'block_reson','!=','Admin Blocked');
            // dd($drivings_block);
            if(auth()->user()->hasRole('Company')){
                $drivings_block = $drivings_block->where('drivers.company_id',auth()->user()->id);
            }
            $drivings_block = $drivings_block->count();
            array_push($block_count,$drivings_block);
        }


        // $drivers = User::join('drivers','users.id','=','drivers.user_id')->where('users.active',0)->get()->count();
        // foreach($drivers as $key => $value){
            // $drivings = Driver::where('type',$key)->count();
        // }
        // $autodrivers = User::join('drivers','users.id','=','drivers.user_id')->where('users.active',0)->where('drivers.type',1)->get()->count();
        // $minidrivers = User::join('drivers','users.id','=','drivers.user_id')->where('users.active',0)->where('drivers.type',2)->get()->count();
        // $sedandrivers = User::join('drivers','users.id','=','drivers.user_id')->where('users.active',0)->where('drivers.type',3)->get()->count();
        // $suvdrivers = User::join('drivers','users.id','=','drivers.user_id')->where('users.active',0)->where('drivers.type',4)->get()->count();
        //   dd( $autodrivers);
        $driverdocum = '';
        foreach ($drivers_active as $key => $value) {
            
            $rating = RequestRating::where('user_id',$value->id)->avg('rating');
            $drivers_active[$key]->rating = (int)$rating;

            $docum = Documents::where('requried',1)->pluck('id')->toArray();
            $driverdocum = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status','>=',1)->count();
            $driverdocum_approved = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status',2)->count();
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
            $drivers_active[$key]->documents = $message;

            $wallet = Wallet::where('user_id',$value->id)->first();
            if(is_null($wallet))
                $drivers_active[$key]->wallet_balance = 0;
            else
                $drivers_active[$key]->wallet_balance = $wallet->balance_amount;
        }
        
        foreach ($drivers_incative as $key => $value) {
            $rating = RequestRating::where('user_id',$value->id)->avg('rating');
            $drivers_incative[$key]->rating = (int)$rating;
            $docum = Documents::where('requried',1)->pluck('id')->toArray();
            $driverdocum = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status','>=',1)->count();
            $driverdocum_approved = DriverDocument::whereIn('document_id',$docum)->where('user_id',$value->id)->where('document_status',2)->count();
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
            $drivers_incative[$key]->documents = $message; 
        }
        
        return view('taxi.driver.DriverList',['drivers_active' => $drivers_active,'drivers_incative' => $drivers_incative,'drivers_block' => $drivers_block,'activecount' => $drivers_active->count(),'blockcount'=>$drivers_incative->count(),'onlinecount'=>$onlinecount,'offlinecount' => $offlinecount,'count'=>$count,'active_count' => $active_count ,'types'=>$types,'driverdocum'=>$driverdocum,'block_count'=>$block_count]);
    }

    public function driverAdd(Request $request)
    {
        $types = Vehicle::where('status',1)->get();
        $country = Country::where('status',1)->get();
        $company = User::role('Company')->where('active',1)->get();
        $userauth = Auth::user();
        $companylogin  = User::role('Company')->where('email',$userauth->email)->where('active',1)->get();

        return view('taxi.driver.AddDriver',['types' => $types,'country' => $country,'company' => $company,'companylogin' => $companylogin]);
    }

    public function driverSave(DriverSaveRequest $request  )
    {
        $driver = $request->all();
        
        $company = "";

        $userPh = User::where('phone_number',$request->phone_number)->role('driver')->first();
       
        if(!is_null($userPh)){
            return response()->json(['status' => false,'message' =>'User completed the registration process. So please login to continue !!'], 403);
        }

        if($request->has('company') && $driver['company']){
            $company = User::where('slug',$driver['company'])->role('Company')->first();
            if($company){
                $drivers = Driver::where('company_id',$company->id)->count();
                if($company->companyDetails->no_of_vehicle <= $drivers){
                    return response()->json(['message' =>'Driver count completed in this company!...','status' => false], 200);
                }
            }
        }

        $user = User::create([
            'firstname' => $driver['first_name'],
            'lastname' => $driver['last_name'],
            'email' => $driver['email'],
            'phone_number' => $driver['phone_number'],
            'country' => $driver['country'],
            'country_code' => $driver['country'],
            'gender' => $driver['gender'],
            'address' => $driver['address'],
            'active' => 0,
            'block_reson' => 'Admin Blocked'
        ]);

        if($driver['company'] == "1"){
            $dumbcompany = DumpCompany::create([
                'company_name' => $driver['company_name'],
                'company_phone_number' => $driver['company_phone_number'],
                'status' => 1,
                'total_no_of_vehicle' => $driver['total_no_of_vehicle'],
                'user_id'=>$user->id,
            ]);
        }
        
        if($request->has('driver_image') && $driver['driver_image'] != ""){

              $filename =  uploadImage('images/profile',$request->file('driver_image'));

            // // AWS S3 Bucket Here
            // $filename = time().'.'.$request->driver_image->extension(); 
            // $path = Storage::disk('s3')->put('images/profile', $request->driver_image);
            // $paths = Storage::disk('s3')->put('', $request->driver_image);
            $user->profile_pic = $filename;
            $user->save();
        }

        $user->assignRole('driver');
        $client = new OauthClients();
        $client->user_id = $user->id;
        $client->name =  $user->firstname;
        $client->secret = $this->generateRandomString(40);
        $client->redirect = 'http://localhost';
        $client->personal_access_client = false;
        $client->password_client = false;
        $client->revoked = false;
        $client->save();

       

        if($request->has('vehicle_model')){
            $fetchVechileModel = VehicleModel::where('slug',$driver['vehicle_model'])->first();
            if($fetchVechileModel){
                $vechile_model = $fetchVechileModel->model_name;
            }else{
                $vechile_model = $driver['car_model'];
            }
        }

        $driverSave = Driver::create([
            'type' => $driver['type'],
            'car_number' => $driver['car_number'],
            'car_model' => $vechile_model,
            'car_year' => $driver['car_year'],
            'car_colour' => $driver['car_colour'],
            'city' => $driver['city'],
            'state' => $driver['state'],
            'pincode' => $driver['pincode'],
            'user_id' => $user->id,
            'company_id' => $company ? $company->id : null,
           // 'service_category' => $driver['service_type'],
            'login_method' => $driver['category'],
            'notes' => $driver['notes'],
            // 'approved_by' => auth()->user()->id,
            'status' => 1,
        ]);

        return response()->json(['message' =>'success','status' => true], 200);
    }

    public function driverDelete($slug)
    {
        $user = User::where('slug',$slug)->first();
        $request = RequestModel::where('driver_id', $user->id)->count();
        if ($request > 0) {
            session()->flash(
                'message',
                'Sorry!. driver cannot delete while on the trip'
            );
            session()->flash('status', false);
            return redirect()->route('driver');
        }else {
            $user = User::where('slug',$slug)->first();
            if($user){
                User::where('slug',$slug)->delete();
                Driver::where('user_id',$user->id)->delete();
            }
            return redirect()->route('driver');
        }
    }

    public function driverActive($slug)
    {

        $user = User::where('slug',$slug)->first();
        if($user && $user->active == 1){
            User::where('slug',$slug)->update(['active' => 0,'block_reson' => "Admin Blocked"]);
           
            $lang = $user->language;
            $push_data = $this->pushlanguage($lang,'driver-blocked');
            if(is_null($push_data)){
               $title = 'Driver Your Account Is Blocked';
               $body = 'Your account is blocked for admin. Please, contact admin.';
               $sub_title = 'Your account is blocked for admin. Please, contact admin.';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            }

            $pushData = ['notification_enum' => PushEnum::DRIVER_BLOCKED];

            dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));

            // dispatch(new SendPushNotification($title, $push_data, $user->device_info_hash, $user->mobile_application_type,1));


        }
        else{
            $documents = Documents::where('requried',1)->pluck('id')->toArray();
            $myDocuments = DriverDocument::where('user_id',$user->id)->where('document_status',2)->whereIn('document_id',$documents)->count();
            if(count($documents) > $myDocuments){
                session()->flash('message','Driver Document not uploaded and approved!...');
                return redirect()->route('driver');
            }
            User::where('slug',$slug)->update(['active' => 1,'block_reson' => '']);

            $lang = $user->language;
            $push_data = $this->pushlanguage($lang,'driver-unblocked');
          //  dd($push_data);
            if(is_null($push_data)){
               $title = 'Congratulations!! Your account has been approved.';
               $body = 'Your account has been approved.';
               $sub_title = 'Your account has been approved.';

            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;

            }

            $pushData = ['notification_enum' => PushEnum::DRIVER_APPROVED];

            dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));

            
            // dispatch(new SendPushNotification("Driver Your Account Is Unblocked",['message' => "Your account is unblocked for admin.",'image' => '','notification_enum' => PushEnum::DRIVER_APPROVED],$user->device_info_hash,$user->mobile_application_type,1));
        }
        return redirect()->route('driver');
    }

    public function driverEdit(Request $request,$slug)
    {
     
     
        $type =  $request->type ? $request->type : "edit";
        $types = Vehicle::where('status',1)->get();
        $country = Country::where('status',1)->get();
        $company = User::role('Company')->where('active',1)->get();
        $subscription = Submaster::get();

        $user = User::where('slug',$slug)->first();

        $models = VehicleModel::where('vehicle_id',$user->driver->type)->get();

        $model = VehicleModel::where('vehicle_id',$user->driver->type)->where('model_name',$user->driver->car_model)->first();
        if($model){
            $selected = '';
        }
        else{
            $selected = 'selected';
        }
        $user->driver->service_category = array_map('trim', explode(',', $user->driver->service_category));

        // dd($models);
        // dd($user->driver->service_category);
        // $user->driver->service_category = explode(',',$user->driver->service_category);

        // $document = Documents::where('status',1)->get();

        // foreach ($document as $key => $value) {
        //     $driverDocument = DriverDocument::where('user_id',$user->id)->where('document_id',$value->id)->first();
        //     $document[$key]->date_required = $document[$key]->expiry_date;
        //     if($driverDocument){
        //         $document[$key]->document_image = $driverDocument->document_image;
        //         $document[$key]->expiry_date = $driverDocument->expiry_date;
        //         $document[$key]->issue_date = $driverDocument->issue_date;
        //         $document[$key]->document_status = $driverDocument->document_status;
        //         $document[$key]->is_uploaded = 1;
        //     }
        //     else{
        //         $document[$key]->document_image = '';
        //         $document[$key]->expiry_date = '';
        //         $document[$key]->issue_date = '';
        //         $document[$key]->document_status = '';
        //         $document[$key]->is_uploaded = 0;
        //     }
        // }
        $documents = DocumentsGroup::where('status',1)->get();
        // dd($documents);
        $data_array = [];
        
        foreach ($documents as $key => $value) {
            
            // dd($value);
            foreach($value->getDocument as $key1 => $value1){
                
                $DriverDocuments = DriverDocument::where('user_id',$user->id)->where('document_id',$value1->id)->first();
                 //dd($DriverDocuments);
                if($DriverDocuments){
                    $value->getDocument[$key1]->document_image = $DriverDocuments->document_images;
                    $value->getDocument[$key1]->expiry_date = $DriverDocuments->expiry_date;
                    $value->getDocument[$key1]->issue_date = $DriverDocuments->issue_date;
                    $value->getDocument[$key1]->document_status = $DriverDocuments->document_status;
                    $value->getDocument[$key1]->is_uploaded = 1;
                }
                else{
                    $value->getDocument[$key1]->document_image = '';
                    $value->getDocument[$key1]->expiry_dated = 0;
                    $value->getDocument[$key1]->issue_date = 0;
                    $value->getDocument[$key1]->is_uploaded = 0;
                }
                
            }
            
            $documents[$key]->get_document = $value->getDocument;
            if(count($value->getDocument) > 0){
                array_push($data_array, $documents[$key]);
            }
               
        }
        $document = $data_array;
        if(!$user)
            return redirect()->route('driver');
// // dd($data_array);
        return view('taxi.driver.EditDriver',['type' => $type,'types' => $types,'country' => $country, 'user' => $user,'driver' => $user,'document' => $document,'subscription' => $subscription,'company' => $company,'models' => $models,'selected' => $selected]);
    }

    public function driverUpdate(DriverSaveRequest $request)
    {
        $driver = $request->all();

        //  dd($driver);
        $userPh = User::where('phone_number',$request->phone_number)->where('slug',$driver['slug'])->role('driver')->count();
        if($userPh > 1){
            return response()->json(['status' => false,'message' =>'User completed the registration process. So please login to continue !!'], 403);
        }

        $user = User::where('slug',$driver['slug'])->first();

        $company = "";
        if($request->has('company') && $driver['company']){
            $company = User::where('slug',$driver['company'])->role('Company')->first();
            if($company){
                $drivers = Driver::where('company_id',$company->id)->count();
                if($company->companyDetails->no_of_vehicle <= $drivers){
                    return response()->json(['message' =>'Driver count completed in this company!...','status' => false], 200);
                }
            }
        }

        $users = User::where('slug',$driver['slug'])->update([
            'firstname' => $driver['first_name'],
            'lastname' => $driver['last_name'],
            'email' => $driver['email'],
            'phone_number' => $driver['phone_number'],
            'country' => $driver['country'],
            'country_code' => $driver['country'],
            'gender' => $driver['gender'],
            'address' => $driver['address']
        ]);
        if($driver['company'] == "1"){
            $dummy_company = DumpCompany::where('slug',$driver['company_slug'])->first();
            if($dummy_company){
                $dumbcompany = DumpCompany::where('slug',$driver['company_slug'])->update([
                    'company_name' => $driver['company_name'],
                    'company_phone_number' => $driver['company_phone_number'],
                    'status' => 1,
                    'total_no_of_vehicle' => $driver['total_no_of_vehicle'],
                    'user_id'=>$user->id,
                ]);
            }
            else{
                $dumbcompany = DumpCompany::create([
                    'company_name' => $driver['company_name'],
                    'company_phone_number' => $driver['company_phone_number'],
                    'status' => 1,
                    'total_no_of_vehicle' => $driver['total_no_of_vehicle'],
                    'user_id'=>$user->id,
                ]);
            }
        }
    

        if($request->has('driver_image') && $driver['driver_image'] != ""){
             $filename =  uploadImage('images/profile',$request->file('driver_image'));

            // AWS S3 Bucket Here
            // $filename = time().'.'.$request->driver_image->extension(); 
            // $path = Storage::disk('s3')->put('images/profile', $request->driver_image);
            // $paths = Storage::disk('s3')->put('', $request->driver_image);
            $user->profile_pic = $filename;
            $user->save();
        }

        if($request->has('vehicle_model')){
            $fetchVechileModel = VehicleModel::where('slug',$driver['vehicle_model'])->first();
            if($fetchVechileModel){
                $vechile_model = $fetchVechileModel->model_name;
            }else{
                $vechile_model = $driver['car_model'];
            }
        }

        $drivers = Driver::where('user_id',$user->id)->first();
        // dd($drivers);
        if(!$drivers){
            $driverSave = Driver::create([
                'type' => $driver['type'],
                'car_number' => $driver['car_number'],
                'car_model' => $vechile_model,
                'car_year' => $driver['car_year'],
                'car_colour' => $driver['car_colour'],
                'user_id' => $user->id,
                'city' => $driver['city'],
                'state' => $driver['state'],
                'pincode' => $driver['pincode'],
                'company_id' => $company ? $company->id : null,
                //'service_category' => $driver['service_type'],
                'login_method' => $driver['category'],
                'notes' => $driver['notes'],    
                // 'approved_by' => auth()->user()->id,            
                'status' => 1
            ]);
        }
        else{
            $driverSave = Driver::where('user_id',$user->id)->update([
                'type' => $driver['type'],
                'car_number' => $driver['car_number'],
                'car_model' => $vechile_model,
                'car_year' => $driver['car_year'],
                'car_colour' => $driver['car_colour'],
                'city' => $driver['city'],
                'state' => $driver['state'],
                'company_id' => $company ? $company->id : null,
                'pincode' => $driver['pincode'],
                'service_category' => $driver['service_type'],
                'login_method' => $driver['category'],
                // 'notes' => $driver['notes'],     
                'approved_by' => auth()->user()->id,           
            ]);
        }

        if($request->has('subscription') && $driver['subscription'] != ""){

            $old_subscription = DriverSubscriptions::where('user_id',$user->id)->where('to_date','>=',NOW())->count();

            if($old_subscription > 0){
                return response()->json(['status' => false, 'message' => 'Sorry!, You are already subscriped.'], 200);
            }

            $subscription = Submaster::where('slug',$driver['subscription'])->first();

            $wallet = Wallet::where('user_id',$user->id)->first();

            if(!$wallet){
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'earned_amount' => 0,
                    'balance_amount' => 0,
                    'amount_spent' => 0
                ]);
            }
           
            if($subscription->amount > $wallet->balance_amount){
                return response()->json(['status' => false, 'message' => 'Sorry! low balaance in wallet.'], 200);
            }

            $driver_subcription = DriverSubscriptions::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'from_date' => NOW(),
                'to_date' => Carbon::now()->addDays($subscription->validity),
                'amount' => $subscription->amount,
                'paid_status' => 1
            ]);

            $wallet->balance_amount -= $subscription->amount;
            $wallet->earned_amount += $subscription->amount;
            $wallet->save();

            $wallet_transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $subscription->amount,
                'purpose' => 'wallet spant amount to subscription',
                'type' => 'SPENT',
                'user_id' => $user->id,
            ]);

        }

        return response()->json(['status' => true,'message' =>'success'], 200);
    }

    public function driverDocument($slug)
    {
        $user = User::where('slug',$slug)->first();

        $document = Documents::where('status',1)->get();

        foreach ($document as $key => $value) {
            $driverDocument = DriverDocument::where('user_id',$user->id)->where('document_id',$value->id)->first();
            $document[$key]->date_required = $document[$key]->expiry_date;
            if($driverDocument){
                $document[$key]->document_image = $driverDocument->document_images;
                $document[$key]->expiry_date = $driverDocument->expiry_date;
                $document[$key]->issue_date = $driverDocument->issue_date;
                $document[$key]->document_status = $driverDocument->document_status;
                $document[$key]->is_uploaded = 1;
            }
            else{
                $document[$key]->document_image = '';
                $document[$key]->expiry_dated = '';
                $document[$key]->issue_dated = '';
                $document[$key]->document_status = '';
                $document[$key]->is_uploaded = 0;
            }
        }
        // dd($document[$key]->expiry_dated);
        //   if($document[$key]->expiry_dated <= Carbon::now())
        return view('taxi.driver.DriverDocumentList',['user' => $user,'document' => $document]);
    }

    public function driverDocumentEdit($user,$slug)
    {        
        $driver_document = Documents::where('slug',$slug)->first();
        $users = User::where('slug',$user)->first();
        $driverDocument = DriverDocument::where('user_id',$users->id)->where('document_id',$driver_document->id)->first();
            //   dd( $driverDocument);
        if($driverDocument){
            $driver_document->document_image = $driverDocument->document_images;
            $driver_document->expiry_dated = $driverDocument->expiry_date;
            $driver_document->issue_dated = $driverDocument->issue_date;
            $driver_document->is_uploaded = 1;
            // dd($driver_document->document_image);
        }
       
        else{
            $driver_document->document_image = '';
            $driver_document->expiry_dated = '';
            $driver_document->issue_dated = '';
            $driver_document->is_uploaded = 0;
        }
        return response()->json(['message' =>'success','data'=>$driver_document], 200);
    }

    public function driverDocumentUpdate(DocumentUploadRequest $request)
    {
        $data = $request->all();

        $users = User::where('slug',$data['driver_id'])->first();
        $document = Documents::where('slug',$data['document_id'])->first();

        $driverDocument = DriverDocument::where('user_id',$users->id)->where('document_id',$document->id)->first();

        $filename =  uploadImage('images/document',$request->file('document_image'));

        // $filename = time().'.'.$request->document_image->extension(); 
        // $path = Storage::disk('s3')->put('images/document', $request->document_image);
        // $paths = Storage::disk('s3')->put('', $request->document_image);

        if($driverDocument){
            // Storage::disk('s3')->delete('images/document/'. $paths);
            deleteImage('images/document',$driverDocument->document_image);
            $driverDocument->document_image = $filename;
            $driverDocument->document_status = 1;
            $driverDocument->expiry_date = $document->expiry_date == 1 ? $data['expiry_date'] : NULL;
            $driverDocument->issue_date = $document->expiry_date == 2 ? $data['expiry_date'] : NULL;
            $driverDocument->exprienc_status = 0;
            $driverDocument->exprience_reson = "";
            $driverDocument->save();
            // dd( $driverDocument->document_image);
        }
        else{
            DriverDocument::create([
                'user_id' => $users->id,
                'document_id' => $document->id,
                'document_image' => $filename,
                'expiry_date' => $document->expiry_date == 1 ? $data['expiry_date'] : NULL,
                'issue_date' => $document->expiry_date == 2 ? $data['expiry_date'] : NULL,
                'document_status' => 1,
                'status' => 1,
                'exprienc_status' => 0,
                'exprience_reson' => ""
            ]);
        }

        $documents = Documents::where('requried',1)->pluck('id')->toArray();
        $driver_documents = DriverDocument::where('user_id',$users->id)->whereIn('document_id',$documents)->count();
        if(count($documents) <= $driver_documents){
            Driver::where('user_id',$users->id)->update(['document_upload_status' => 4]);
        }

        return response()->json(['message' =>'success'], 200);
    }

    public function driverDocumentApproved(Request $request)
    {
        $data = $request->all();
        $users = User::where('slug',$data['driver_id'])->first();

        $approved = Documents::whereIn('slug',explode(",", $data['approved_document_id']))->pluck('id')->toArray();
        $denaited = Documents::whereIn('slug',explode(",", $data['denaited_document_id']))->pluck('id')->toArray();
        $driverDocument = DriverDocument::where('user_id',$users->id)->whereIn('document_id',$approved)->update([
            'document_status' => 2,
            'exprience_reson' => ""
        ]);
        $driverDocument = DriverDocument::where('user_id',$users->id)->whereIn('document_id',$denaited)->update([
            'document_status' => 0,
            'exprience_reson' => "This document is denaited"
        ]);

        $old_subscription = DriverSubscriptions::where('user_id',$users->id)->where('to_date','>=',NOW())->count();

        if($old_subscription == 0){
            $driverSave = Driver::where('user_id',$users->id)->update([
                'subscription_type' => $data['subscription_type'],
                'approved_by' => auth()->user()->id,   
            ]);
        }

        $documents = Documents::where('requried',1)->where('status',1)->pluck('id')->toArray();

        $myDocuments = DriverDocument::where('user_id',$users->id)->where('document_status',2)->whereIn('document_id',$documents)->count();
        $myDeniteDocuments = DriverDocument::where('user_id',$users->id)->where('document_status',0)->count();
        $driver_documents = DriverDocument::where('user_id',$users->id)->whereIn('document_id',$documents)->count();
        if(count($documents) <= $myDocuments){
            Driver::where('user_id',$users->id)->update(['document_upload_status' => 4]);
            $users->active = 1;
            $users->block_reson = "";
            $users->save();

           

           dispatch(new SendPushNotification("Driver Your Account Is Unblocked","Driver Your Account Is Unblocked",['message' => "Your account is unblocked for admin.",'image' => '','notification_enum' => PushEnum::DRIVER_APPROVED],$users->device_info_hash,$users->mobile_application_type,0));
        }
        else{
            
                $documents = Documents::where('requried',1)->where('status',1)->pluck('id')->toArray();
                $myDocuments = DriverDocument::where('user_id',$users->id)->where('document_status',2)->whereIn('document_id',$documents)->get();
                dd($myDocuments);
                if(count($documents) > $myDocuments){
                    session()->flash('message','Driver Document not uploaded and approved!...');
                    // return redirect()->route('driverDocumentEdit');
                    return response()->json(['message' =>'Driver Document not uploaded and approved!...'], 200);
                    
                }
            $users->active = 0;
            $users->block_reson = "Driver Document not Uploaded and Approved";
        
            $users->save();
            $title = Null;
            $body = '';
            $lang = $users->language;
            $push_data = $this->pushlanguage($lang,'driver-blocked');
            if(is_null($push_data)){
               $title = 'Driver Your Account Is Blocked';
               $body = 'Your rejected multiple trips for continue. So, your account is blocked. Please, contact admin.';
            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
            }
            dispatch(new SendPushNotification("Driver Your Account Is Blocked","Driver Your Account Is Blocked",['message' => "Your account is blocked for admin. Please, contact admin.",'image' => '','notification_enum' => PushEnum::DRIVER_BLOCKED],$users->device_info_hash,$users->mobile_application_type,0));
        }
        if(count($documents) <= $myDocuments){
            Driver::where('user_id',$users->id)->update(['document_upload_status' => 0]);
        }
        elseif($myDeniteDocuments){
            Driver::where('user_id',$users->id)->update(['document_upload_status' => 1]);
        }

        return response()->json(['message' =>'success'], 200);
    }

    public function driverTripDetails($slug)
    {
        $users = User::where('slug',$slug)->first();

        //  dd($users->driverRequests);

        return view('taxi.driver.DriverTripList',['user' => $users]);
    }

    public function driverDetails($slug)
    {
        $users = User::where('slug',$slug)->first();

        //  


        $wallet_driver_refernce_amount = Settings::where('name',"wallet_driver_refernce_amount")->first();

        $users->rating = RequestRating::where('user_id',$users->id)->avg('rating');

        $users->wallet = Wallet::where('user_id',$users->id)->avg('balance_amount');

        $users->referal_count = User::where('user_referral_code',$users->referral_code)->whereNotNull('user_referral_code')->count();

        if($wallet_driver_refernce_amount){
            $users->referal_amount = $users->referal_count * (int)$wallet_driver_refernce_amount->value;
        }
        else{
            $users->referal_amount = 0;
        }

        $subscription = DriverSubscriptions::where('user_id',$users->id)->where('from_date','<=',NOW())->where('to_date','>=',NOW())->first();

        if($subscription){
            $datetime1 = new DateTime(NOW());
            $datetime2 = new DateTime($subscription->to_date);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');
            $subscription->balance_days = $days;
        }
            
        $users->fine_amount = 0;
        $users->bonus_amount = 0;
        $users->driverRequest = RequestModels::where('driver_id',$users->id)->limit(4)->get();

        // dd($users->driverRequests);

        $users->fine_amount = Fine::where('user_id',$users->id)->sum('fine_amount');

      //  dd($users->fine_amount);

      $drivers_online = User::role('driver')->with(['getCountry','driver','driver.vehicletype'])->where('online_by',1)->get();
      

     
          $drivers_online->trip_completed = RequestModels::where('driver_id',$users->id)->where('is_completed',1)->count();
          $drivers_online->trip_cancelled = RequestModels::where('driver_id',$users->id)->where('is_cancelled',1)->count();
          $driver_logs = DriverLogs::where('driver_id',$users->id)->where('date',date('Y-m-d'))->get();
          $today = '00:00:00';
          foreach ($driver_logs as $keys => $values) {
              if($values->online_time && $values->offline_time){
                  $today = $this->timeAdd($today,$values->working_time);
              }
              if($values->online_time && !$values->offline_time){
                  $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                  $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                  // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S'); 
                  $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                  $today = $this->timeAdd($today,$login_hours);
              }
          }
          
          $drivers_online->today_working = $today;
          
          $questions = InvoiceQuestions::where('status',1)->get();
       
        foreach ($questions as $key => $value) {
            $total = RequestQuestions::where('driver_id',$users->id)->where('question_id',$value->id)->count();
          $up_request_count = RequestQuestions::where('driver_id',$users->id)->where('answer',"YES")->where('question_id',$value->id)->count();
          $down_request_count = RequestQuestions::where('driver_id',$users->id)->where('answer',"NO")->where('question_id',$value->id)->count();
          
          if($up_request_count > 0) {
              $questions[$key]->up_percentage = ($up_request_count / $total) * 100;
              
          } else {
              $questions[$key]->up_percentage = 0;
          }
          
          // dd($down_request_count);
          if($down_request_count > 0) {
              $questions[$key]->down_percentage = ($down_request_count / $total) * 100;
              
          } else {
              $questions[$key]->down_percentage = 0;
          }
        //   dump($questions['up_percentage'],$questions['down_percentage']);
         
        }
         
        
        return view('taxi.driver.DriverDetails',['user' => $users,'subscription' => $subscription,'drivers_online' => $drivers_online, 'questions' => $questions]);
    }

    public function driverRefernceList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $refernce_list = User::where('user_referral_code',$users->referral_code)->get();

        foreach ($refernce_list as $key => $value) {
            $refernce_list[$key]->referal_amount = ReferalAmountList::where('user_id',$value->id)->where('referal_user_id',$users->id)->sum('amount');
            // dd($value->hasRole('user'));
            if($value->hasRole('driver')){
                $refernce_list[$key]->user_role = "Driver";
            }
            if($value->hasRole('user')){
                $refernce_list[$key]->user_role = "User";
            }
        }

        return view('taxi.driver.ReferedDriverList',['refernce_list' => $refernce_list]);
    }

    public function driverComplaintsList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $complaints_list = UserComplaint::where('user_id',$users->id)->get();

        return view('taxi.driver.DriverComplaintsList',['complaints_list' => $complaints_list,'users' => $users]);
    }

    public function driverRatingsList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $ratings_list = RequestRating::where('user_id',$users->id)->get();

        return view('taxi.driver.DriverRatingsList',['ratings_list' => $ratings_list]);
    }

    public function driverWorkingHours($slug)
    {
        $users = User::where('slug',$slug)->first();

        return view('taxi.driver.DriverLogsList',['user' => $users]);
    }

    public function driverFineList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $fine_list =  Fine::where('user_id',$users->id)->get();

        $fine_total=  Fine::where('user_id',$users->id)->sum('fine_amount');  

        return view('taxi.fine.index',['fine_list' => $fine_list,'fine_total' => $fine_total,'users' => $users]);
    }

    public function fineSave(Request $request,$slug)
    {
        $data = $request->all();

        $users = User::where('slug',$slug)->first();

        $fine = Fine::create([
            'user_id' => $users->id,
            'fine_amount' => $data['fine_amount'],
            'description' => $data['description'],
        ]);
   

        return response()->json(['message' =>'success','user' => $users], 200);

    }

    public function DriverLogsLists(Request $request)
    {

        $drivers_log = User::role('driver')->get();
        return view('taxi.driver.DriverLogsLists',['drivers_log' => $drivers_log]);
    }

    public function DriverGetModel($slug)
    {
        $models = VehicleModel::where('vehicle_id',$slug)->get();
        return response()->json(['message' =>'success','models' => $models], 200);
    }


    public function timeAdd($start_time,$end_time)
    {
        $hovers = date('H',strtotime($start_time)) * 60 * 60;
        $minits = date('i',strtotime($start_time)) * 60;
        $seconds = date('s',strtotime($start_time));
        $start = $hovers+$minits+$seconds;
        
        $hovers = date('H',strtotime($end_time)) * 60 * 60;
        $minits = date('i',strtotime($end_time)) * 60;
        $seconds = date('s',strtotime($end_time));
        $end = $hovers+$minits+$seconds;

        $value = $start + $end;

        $dt = Carbon::now();
        // $days = $dt->diffInDays($dt->copy()->addSeconds($value));
        $hours = $dt->diffInHours($dt->copy()->addSeconds($value));
        $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subHours($hours));
        $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($value)->subHours($hours)->subMinutes($minutes));
        return $hours.":".$minutes.":".$seconds;
    }

}
