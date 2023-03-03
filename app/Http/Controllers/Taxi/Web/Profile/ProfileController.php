<?php

namespace App\Http\Controllers\Taxi\Web\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\boilerplate\RolePermission\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\admin\UserRequest;
use App\Http\Requests\Taxi\Web\ProfileEditRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;


use App\Models\boilerplate\Languages;

class ProfileController extends Controller
{
    public function Profile(Request $request)
    {
        // $user = auth()->user()->id;
        $user = User::where('id',auth()->user()->id)->first();
        // $roles = Role::whereNotIn('name',['user','driver','Company'])->get();
         $languages = Languages::get();
        //   dd($user->profile_pic);
        return view('taxi.myprofile.profile',['user' => $user,'languages' => $languages]);
    }

    public function profileUpdate(Request $request)
    {
        $data = $request->all();
        $user = User::where('slug',$data['user_id'])->first();
        if(is_null($user))
        {
            $message = 'no data';
            return response()->json(['message' =>$message], 200);
        }else{
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];
            $user->phone_number = $data['phone_number'];
            $user->gender = $data['gender'];
            $user->language = $data['language'];
            if($request->file('profile_pic') != ""){
             $filename =  uploadImage('images/profile',$request->file('profile_pic'));
            // $filename = time().'.'.$request->profile_pic->extension(); 
            // $path = Storage::disk('s3')->put('images/profile', $request->profile_pic);
            // $paths = Storage::disk('s3')->put('', $request->profile_pic);
    
                $user->profile_pic = $filename;
            }
        
            $user->update();

        }
        // $user = User::where('slug',$data['user_id'])->update([
        //     'firstname' => $data['firstname'],
        //     'lastname' => $data['lastname'],
        //     'phone_number' => $data['phone_number'],
        //     'gender' => $data['gender'],
        //     'language' => $data['language'],
            
        // ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function profileEdit($slug)
    {
        $user = User::where('slug',$slug)->first();
        // dd( $user);

        return response()->json(['message' =>'success','user' => $user], 200);
    }

    public function passwordChange(Request $request)
    {
        $data = $request->all();

        $user = User::where('slug',$data['user_slug'])->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' =>'success'], 200);
    }
}