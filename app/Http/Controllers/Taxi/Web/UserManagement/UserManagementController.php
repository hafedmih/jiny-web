<?php

namespace App\Http\Controllers\Taxi\Web\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Fine;
use App\Models\User;
use App\Models\taxi\ReferalAmountList;
use App\Models\boilerplate\Country;
use App\Models\taxi\Wallet;
use App\Models\taxi\UserComplaint;
use App\Models\taxi\Settings;
use App\Models\taxi\WalletTransaction;
use App\Constants\PushEnum;
use App\Traits\CommanFunctions;
use App\Jobs\SendPushNotification;
use App\Models\boilerplate\OauthClients;
use App\Models\boilerplate\RolePermission\Role;
use App\Http\Requests\admin\UserRequest;
use App\Models\boilerplate\Languages;
use App\Traits\RandomHelper;
use Carbon;
use DB;
use App\Models\taxi\Requests\Request as RequestModel;
class UserManagementController extends Controller
{
    use RandomHelper,CommanFunctions;
    public function userManage(Request $request)
    {

        $users = User::role('user')->with(['getCountry'])->where('active',1)->latest()->get();
        $block_users = User::role('user')->with(['getCountry'])->where('active',0)->latest()->get();

        $onlinecount = User::role('user')->where('active',1)->where('online_by',1)->get()->count();
        $offlinecount = User::role('user')->where('active',1)->where('online_by',0)->get()->count();
        
        foreach ($users as $key => $value) {
            
            $wallet = Wallet::where('user_id',$value->id)->first();
            if(is_null($wallet))
                $users[$key]->wallet_balance = 0;
            else
                $users[$key]->wallet_balance = $wallet->balance_amount;
        }
        
        

        $languages = Languages::where('status',1)->get();

        $country = Country::where('status',1)->get();

        return view('taxi.user-management.userlist',['users' => $users,'block_users' => $block_users,'activecount' => $users->count(),'blockcount'=>$block_users->count(),'onlinecount'=>$onlinecount,'offlinecount' => $offlinecount,'languages' => $languages,'country' => $country]);
    }

  
    public function userDelete($slug)
    {
        $user = User::where('slug',$slug)->first();
        if($user){
            User::where('slug',$slug)->delete();
            Driver::where('user_id',$user->id)->delete();
        }
        return redirect()->route('userManage');
    }

    public function userActive($slug)
    {

        $user = User::where('slug',$slug)->first();
        if($user && $user->active == 1){
            User::where('slug',$slug)->update(['active' => 0,'block_reson' => "Admin Blocked"]);
        }
        else{
            User::where('slug',$slug)->update(['active' => 1,'block_reson' => ""]);
        }
        return redirect()->route('userManage');
    }

  

    public function userView($slug)
    {
        $user = User::where('slug',$slug)->first();

        if(!$user)
            return redirect()->route('userManage');

        $user->referal_count = User::where('user_referral_code',$user->referral_code)->whereNotNull('user_referral_code')->count();
      
        
        return view('taxi.user-management.viewlist',['user' => $user]);
    }

    public function userTripsList($slug)
    {

        $user = User::where('slug',$slug)->first();

        if(!$user)
            return redirect()->route('userManage');

        return view('taxi.user-management.UserTripList',['user' => $user]);
    }

    

    public function userWallet($slug)
    {
        $user = User::where('slug',$slug)->first();
        $wallet = Wallet::where('user_id',$user->id)->first();
        $currency = RequestModel::pluck('requested_currency_symbol')->first();

        
        if(!$wallet){
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'earned_amount' => 0,
                'balance_amount' => 0,
                'amount_spent' => 0
            ]);
        }

        $wallet_transaction = WalletTransaction::with('getRequest')->where('user_id',$user->id)->orderBy('id','desc')->get();
       
        $earn_wallet_transaction =  WalletTransaction::where('user_id',$user->id)->where('type','EARNED')->orderBy('id','desc')->limit(10)->pluck('amount')->toArray();
        $spent_wallet_transaction =  WalletTransaction::where('user_id',$user->id)->where('type','SPENT')->orderBy('id','desc')->limit(10)->pluck('amount')->toArray();
        
        if(!$user)
            return redirect()->route('userManage');

        return view('taxi.user-management.userwallet',['user' => $user,'wallet' => $wallet,'wallet_transaction' => $wallet_transaction,'earn_wallet_transaction' => $earn_wallet_transaction,'spent_wallet_transaction' => $spent_wallet_transaction,'currency' => $currency]);
    }


    public function walletSave(Request $request,$slug)
    {
        $data = $request->all();
        $user = User::where('slug',$slug)->first();


        $wallet = Wallet::create([
            'user_id' => $user->id,
            'earned_amount' => $data['amount'],
        ]);

       // dd($wallet);

        // $wallet_transaction = WalletTransaction::create([
        //     'phone_number' => $data['phone_number'],
        //     'description' => $data['description'],
        //     'title' => $data['title'],
        // ]);

        return response()->json(['message' =>'success'], 200);

    }

    public function walletSaveAmount(Request $request)
    {

        $data = $request->all();
        $user = User::where('slug',$data['user_id'])->first();

        $wallet = Wallet::where('user_id',$user->id)->first();

        if($wallet){
            $wallet->earned_amount += $data['amount'];
            $wallet->balance_amount += $data['amount'];
            $wallet->save();
        }
        else{
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'earned_amount' => $data['amount'],
                'balance_amount' => $data['amount'],
                'amount_spent' => 0,
            ]);
        }

        $wallet_transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'purpose' => 'wallet amount added successfully',
            'type' => 'EARNED',
            'user_id' => $user->id
        ]);

        $driver_block_wallet_balance = Settings::where('name',"driver_block_wallet_balance")->first();
        $driver_block_wallet_balance = $driver_block_wallet_balance ? $driver_block_wallet_balance->value : '0';

        $wallet_check =Wallet::where('user_id',$user->id)->first();
        
        if($wallet_check->balance_amount > $driver_block_wallet_balance)
        {
            $user_update = User::where('slug',$data['user_id'])->update(['active' => 1,'block_reson' => '']);
            
                $title = Null;
                $body = '';
                $lang = $user->language;
                $push_data = $this->pushlanguage($lang,'driver-unblocked');
                if(is_null($push_data)){
                   $title = 'Driver Your Account Is UnBlocked';
                   $body = 'Driver Your Account Is UnBlocked';
                   $sub_title = 'Driver Your Account Is UnBlocked';

                }else{
                    $title = $push_data->title;
                    $body =  $push_data->description;
                    $sub_title =  $push_data->description;

                }

                $pushData = ['notification_enum' => PushEnum::DRIVER_APPROVED];

                dispatch(new SendPushNotification($title,$sub_title,$pushData,$user->device_info_hash,$user->mobile_application_type,0));

        }

               



        return response()->json(['message' =>'success'], 200);

    }

    public function userComplaintsList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $complaints_list = UserComplaint::where('user_id',$users->id)->get();

        return view('taxi.user-management.UserComplaintsList',['complaints_list' => $complaints_list]);
    }

    public function userRatingsList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $ratings_list = $users->rating;

        return view('taxi.user-management.UserRatingsList',['ratings_list' => $ratings_list]);
    }

    public function userFineList($slug)
    {
        $users = User::where('slug',$slug)->first();

        $fine_list =  Fine::where('user_id',$users->id)->get();

        $fine_total=  Fine::where('user_id',$users->id)->sum('fine_amount');  
    
         return view('taxi.fine.index',['fine_list' => $fine_list,'fine_total' => $fine_total,'users' =>  $users]);
    }
    public function userRefernceList($slug)
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

        return view('taxi.user-management.userreferalList',['refernce_list' => $refernce_list]);
    }

    public function usermanagementSave(Request $request)
    {
        $data = $request->all();

        $user = User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'language' => $data['language'],
            'country_code' => $data['country_code'],
            'active' => '1'
        ]);
        $user->assignRole('user');

        $client = new OauthClients();
        $client->user_id = $user->id;
        $client->name =  $data['first_name'];
        $client->secret = $this->generateRandomString(40);
        $client->redirect = 'http://localhost';
        $client->personal_access_client = false;
        $client->password_client = false;
        $client->revoked = false;
        $client->save();

        return response()->json(['message' =>'success'], 200);
    }

    public function usermanagementEdit($slug)
    {
        $user = User::with('roles')->where('slug',$slug)->first();

        return response()->json(['message' =>'success','user' => $user], 200);
    }

    public function usermanagementDelete($slug)
    {

        $user = User::where('slug',$slug)->first();
        
        $request = RequestModel::where('user_id', $user->id)->count();

        if ($request > 0) {
            session()->flash(
                'message',
                'Sorry!. user cannot delete while on the trip'
            );
            session()->flash('status', false);
            return redirect()->route('userManage');
        }else {
             $user = User::where('slug',$slug)->delete();
            return redirect()->route('userManage');
        }
        
    }

    public function usermanagementActive($slug)
    {
        $user = User::where('slug',$slug)->first();

        if($user->active == '1'){
            $user->active = 0;
        }
        else{
            $user->active = 1;
        }
        $user->save();

        return redirect()->route('userManage');
    }

    public function usermanagementUpdate(UserRequest $request)
    {
        $data = $request->all();
        $user = User::where('slug',$data['user_id'])->update([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'language' => $data['language'],
            'country_code' => $data['country_code'],

        ]);

        return response()->json(['message' =>'success'], 200);
    }


}
