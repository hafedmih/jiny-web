<?php

namespace App\Http\Controllers\boilerplate\Web\CompanyManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\taxi\Driver;
use App\Models\boilerplate\RolePermission\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\admin\CompanyRequest;
use Illuminate\Database\Eloquent\Builder;

use App\Models\boilerplate\Languages;
use App\Models\boilerplate\CompanyDetails;

class CompanyController extends Controller
{
    public function company(Request $request)
    {
        if(auth()->user()->hasRole('Super Admin')){
            $user = User::role(['Company'])->get();
        }
        else{
            $user = User::role(['Company'])->where('created_by',auth()->user()->id)->get();
        }
        $roles = Role::where('name','Company')->get();
        $languages = Languages::get();
        return view('boilerplate.company.Company',['user' => $user,'roles' => $roles,'languages' => $languages]);
    }

    public function companySave(CompanyRequest $request)
    {
        $data = $request->all();
        if(auth()->user()->hasRole('Super Admin')){
            $user = User::create([
                'firstname' => $data['first_name'],
                'lastname' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'gender' => $data['gender'],
                'language' => $data['language'],
                'address' => $data['address'],
                'emergency_number' => $data['emergency_number'],
                'active' => '1'
            ]);
            $user->assignRole($data['role']);

            $company = CompanyDetails::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'company_code' => $data['company_code'],
                'no_of_vehicle' => $data['no_of_vehicles'],
                'alternative_number' => $data['alternative_number'],
                'commission' => $data['commission'],
                'status' => 1
            ]);
        }
        else{
            $user = User::create([
                'firstname' => $data['first_name'],
                'lastname' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'gender' => $data['gender'],
                'language' => $data['language'],
                'address' => $data['address'],
                'emergency_number' => $data['emergency_number'],
                'created_by' => auth()->user()->id,
                'active' => '1'
            ]);
            $user->assignRole($data['role']);
    
            $company = CompanyDetails::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'company_code' => $data['company_code'],
                'no_of_vehicle' => $data['no_of_vehicles'],
                'alternative_number' => $data['alternative_number'],
                'commission' => $data['commission'],
                'created_by' => auth()->user()->id,
                'status' => 1
            ]);
        }
        return response()->json(['message' =>'success'], 200);
    }

    public function companyEdit($slug)
    {
        $user = User::with('roles','companyDetails')->where('slug',$slug)->first();

        return response()->json(['message' =>'success','user' => $user], 200);
    }

    public function companyDelete($slug)
    {
        // $user = User::where('slug',$slug)->delete();
        $user = User::where('slug',$slug)->first();
        // dd($user);
            $company = Driver::where('company_id',$user->id)->first() ? Driver::where('company_id',$user->id)->first()->id : '' ;
       
        
        if($company > 0){
            session()->flash('message',"This Company cannot be deleted,it has users");
            return back();
        }
        $user = User::where('slug',$slug)->delete();
        return redirect()->route('company');
    }

    public function companyActive($slug)
    {
        $user = User::where('slug',$slug)->first();

        if($user->active == '1'){
            $user->active = 0;
        }
        else{
            $user->active = 1;
        }
        $user->save();

        return redirect()->route('company');
    }

    public function companyUpdate(CompanyRequest $request)
    {
        $data = $request->all();
        $user = User::where('slug',$data['user_id'])->update([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'language' => $data['language'],
            'address' => $data['address'],
            'emergency_number' => $data['emergency_number']
        ]);

        $user = User::where('slug',$data['user_id'])->first();

        $company = CompanyDetails::where('user_id',$user->id)->first();
        if(!$company){
            $company = CompanyDetails::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'company_code' => $data['company_code'],
                'no_of_vehicle' => $data['no_of_vehicles'],
                'alternative_number' => $data['alternative_number'],
                'commission' => $data['commission'],
                'status' => 1
            ]);
        }
        else{
            $company->no_of_vehicle = $data['no_of_vehicles'];
            $company->company_name = $data['company_name'];
            $company->company_code = $data['company_code'];
            $company->no_of_vehicle = $data['no_of_vehicles'];
            $company->alternative_number = $data['alternative_number'];
            $company->commission = $data['commission'];
            $company->save();
        }

        return response()->json(['message' =>'success'], 200);
    }

    public function companyPasswordUpdate(CompanyRequest $request)
    {
        $data = $request->all();

            $user = User::where('slug',$data['user_slug'])->update([
                'password' => Hash::make($data['password']),
            ]);
       
        return response()->json(['message' =>'success'], 200);
    }
}
