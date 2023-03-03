<?php

namespace App\Http\Controllers\boilerplate\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\boilerplate\RolePermission\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\admin\UserRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\RandomHelper;
use App\Models\boilerplate\OauthClients;

use App\Models\boilerplate\Languages;

class UserController extends Controller
{
    use RandomHelper;

    function __construct()
    {
        // $this->middleware('permission:list-user', ['only' => ['index']]);
        $this->middleware('permission:new-user', ['only' => ['usersSave']]);
        $this->middleware('permission:edit-user', ['only' => ['usersEdit','usersUpdate']]);
        $this->middleware('permission:delete-user', ['only' => ['usersDelete']]);
        $this->middleware('permission:status-change-user', ['only' => ['usersDelete']]);
        $this->middleware('permission:user-change-password', ['only' => ['usersPasswordUpdate']]);
    }

    public function user(Request $request)
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name','!=', 'user')->where('name','!=', 'driver')->where('name','!=', 'Company');
        })->get();

        $roles = Role::whereNotIn('name',['user','driver','Company'])->get();
        
        $languages = Languages::get();
        return view('boilerplate.user.User',['user' => $user,'roles' => $roles,'languages' => $languages]);
    }

    public function usersSave(UserRequest $request)
    {
        $data = $request->all();

        $user = User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'language' => $data['language'],
            'address' => $data['address'],
            'emergency_number' => $data['emergency_number'],
            'active' => '1'
        ]);
        $user->assignRole($data['role']);

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

    public function usersEdit($slug)
    {
        $user = User::with('roles')->where('slug',$slug)->first();

        return response()->json(['message' =>'success','user' => $user], 200);
    }

    public function usersDelete($slug)
    {
        $user = User::where('slug',$slug)->delete();

        return redirect()->route('user');
    }

    public function usersActive($slug)
    {
        $user = User::where('slug',$slug)->first();

        if($user->active == '1'){
            $user->active = 0;
        }
        else{
            $user->active = 1;
        }
        $user->save();

        return redirect()->route('user');
    }

    public function usersUpdate(UserRequest $request)
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

        \DB::table('model_has_roles')->where('model_id',$user->id)->delete();
        $user->assignRole($data['role']);

        return response()->json(['message' =>'success'], 200);
    }

    public function usersPasswordUpdate(UserRequest $request)
    {
        $data = $request->all();

        $user = User::where('slug',$data['user_slug'])->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' =>'success'], 200);
    }
}
