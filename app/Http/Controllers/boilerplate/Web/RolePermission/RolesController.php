<?php

namespace App\Http\Controllers\boilerplate\Web\RolePermission;

use Illuminate\Http\Request;
// use Input;
use App\Http\Controllers\Controller; 
use App\Http\Requests;
// use DB;
use Auth;
use Artisan;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

use App\Models\boilerplate\RolePermission\Role;
use App\Models\boilerplate\RolePermission\RoleUser;
use App\Models\boilerplate\RolePermission\UserPublicKey;
use App\Models\boilerplate\RolePermission\Permission;
use App\Models\boilerplate\RolePermission\PermissionRole;
use App\Models\User;

use Entrust;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-list', ['only' => ['index','show']]);
        $this->middleware('permission:new-role', ['only' => ['create','store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
        $this->middleware('permission:assign-role-permission', ['only' => ['rolePermission','updateRolePermission']]);
    }   
    public function index(Request $request){ 
        // if(Auth::user()->hasPermissionTo('role-list')){
            $rolesList = Role::get();

            // $rolesList->appends($request->except('page'));
            return view('boilerplate.roles-permission.roles.rolesList', ['rolesList' => $rolesList]);
            
        // }else{
        //     abort(403);
        // }
           
        
    }
    public function store(Request $request){ 
       
       $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|max:255|unique:roles',
            'display_name' => 'required|max:255|unique:roles',
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
        try{
            $role = new Role();
            $role->name = strip_tags(trim($request->input('name')));
            $role->display_name = strip_tags(trim($request->input('display_name')));
            $role->description = strip_tags(trim($request->input('description')));        
            $role->guard_name = 'web';        
            $role->save();

            return response()->json(['message' =>'success'], 200);   
        } catch (\Exception $e) {
            return response()->json(['message' =>'failure.'.$e], 400); 
        }        
    }

    public function edit($slug){ 
        
        $role = Role::where('slug', $slug)->first();
        if(!is_null($role)){
            return response()->json(['message' =>'success','data' => $role], 200);
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        } 
    }

    public function update(Request $request, $slug){ 
        
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|max:255',
            'display_name' => 'required|max:255',
        ]);  
        if($validator->fails()){
                return response()->json(['error' => $validator->errors()], 422);
        } 

        $role = Role::where('slug', $slug)->first();
        if(!is_null($role)){
            try{
                $role->name = strip_tags(trim($request->input('name')));
                $role->display_name = strip_tags(trim($request->input('display_name')));
                $role->description = strip_tags(trim($request->input('description')));
                $role->save();
                return response()->json(['message' =>'success'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' =>'failure.'.$e], 400); 
            }  
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        }            
        return back();
    }

    public function destroy(Request $request,$slug){ 
       
        $role = Role::where('slug', $slug)->first();
        
        $rolehasusers = DB::table('model_has_roles')
                 ->where('role_id',$role->id)
                 ->get();

         $data = ['cannot delete'];  
        // dd($role);
        if(count($rolehasusers)>0){
            // return redirect('roles')->with(['success' => 'Record cannot be deleted!']);
            // flash('Record does not Exist !!!', 'danger')->important();
            //   return response()->json([
            //       'success' => 'Record cannot be deleted!']);
            session()->flash('message','Cannot delete the role');
            session()->flash('status',false);
             return back();
            // return response()->json([$data]);
      

        }else{
            Role::whereId($role->id)->delete();
            // return response()->json([
            //     'success' => 'Record has been deleted successfully!']);
            return back();
        }       
         

        
        
        // dd($role);
        // if(!is_null($role)){
        //     try{
        //         Role::whereId($role->id)->delete();
        //         return back();
        //     } catch (\Exception $e) {
        //         return response()->json(['message' =>'failure.'.$e], 400); 
        //     }  
        // }else{
        //     flash('Record does not Exist !!!', 'danger')->important();
        //     return back();
        // }
    }

    public function rolePermission(Request $request,$slug){
       
        $role = Role::where('slug', $slug)->first();
        if(!$role){
            abort(404);
        }
        $currentUser = User::find(Auth::id());
        if(is_null($currentUser))
            abort(403);
        $role_permissions = null;
       
        $role_permissions = Permission::leftJoin('role_has_permissions', function($join)  use ($role) {
                $join->on('permissions.id', '=', 'role_has_permissions.permission_id')->where('role_has_permissions.role_id', $role->id);
            })->orderBy('permissions.category', 'asc')->get();


        $permission_category = $role_permissions->pluck('category')->unique();
        foreach ($role_permissions as $key => $value) {
            $role_permissions[$key]['assigned'] = false;
            if($role_permissions[$key]['permission_id'] && $role_permissions[$key]['role_id'])
                $role_permissions[$key]['assigned'] = true;

            unset($role_permissions[$key]['created_at']);
            unset($role_permissions[$key]['updated_at']);
        }

        return view('boilerplate.roles-permission.roles.rolePermission', ['role'=> $role,'permission_category'=> $permission_category, 'role_permissions' => $role_permissions]);
    }
    public function updateRolePermission($roleSlug, $permission_name){
        
        $role = Role::where('slug', $roleSlug)->first();
        if(!$role){
            abort(404);
        }        

        $permission = Permission::where('name', $permission_name)->first();
        if(!$permission){
            abort(404);
        }        

        $currentUser = User::find(Auth::id());   
        if(!$permission){
            abort(404);
        }        

        $role_permissions = PermissionRole::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
        if($role_permissions){
            // $role->revokePermissionTo($permission_name);
            PermissionRole::where('role_id', $role->id)->where('permission_id', $permission->id)->delete();
            flash('Role Permission Updated !!!', 'success')->important();
        }
        else{
            // $role->givePermissionTo($permission_name);
            PermissionRole::create(['permission_id'=> $permission->id, 'role_id'=> $role->id]);
            flash('Role Permission Updated !!!', 'success')->important();
        }
        Artisan::call('cache:clear');
        return back();    
    }

    // Encryption Decryption : START

    // public function getRoleKeys(){

    //     if(!Entrust::can('create-ticket')){
    //         return response()->json(['data' =>'Forbidden'], 403);
    //     }
    //     $getRoleKeys = Role::select('slug','public_key','name')->where('slug','super-admin')->orWhere('slug','admin')->orWhere('slug','hub-manager')->orWhere('slug','support-team-india')->orWhere('slug','support-team-canada')->get();
    //     return response()->json(['list'=> $getRoleKeys], 200);
    // }

    // Get User Roles Keys - END


    // public function getRoles(){
        
    //     $getRoleKeys = RoleUser::select('role_id','public_key','private_key')->where('user_id',Auth::id())->get();
        
    //     return response()->json(['list'=> $getRoleKeys], 200);
    // }

    // public function roleAssign(Request $request, $slug){
       
    //     $validator = Validator::make($request->all(),[
    //         'role_id' => 'required',
    //         'public_key' => 'required|min:130|max:130',
    //         'private_key' => 'required',
    //     ]);  
    //     if($validator->fails()){
    //         return response()->json(['data' => $validator->errors()], 422);
    //     }
    //     $roleSlug = trim($request->input('role_id'));
    //     $public_key = trim($request->input('public_key'));
    //     $private_key = trim($request->input('private_key'));
       
    //     $checkRoleExists = Role::where('id',$roleSlug)->where('public_key',$public_key)->first();

    //     if(is_null($checkRoleExists)){
    //         return response()->json(['data' => "Not Found",'error'=>'true'],404);
    //     }
    //     $checkUserExists = User::where('slug',$slug)->first();

    //     if(is_null($checkUserExists)){
    //         return response()->json(['data' => "Not Found",'error'=>'true'],404);
    //     }
    //     $checkAssignExists = RoleUser::where('user_id',$checkUserExists->id)->where('role_id',$checkRoleExists->id)->first();
    //     if(!is_null($checkAssignExists)){
    //         $checkUserExists->detachRole($checkRoleExists);            
    //     }else{
    //         $roleUser = new RoleUser();
    //         $roleUser->user_id = $checkUserExists->id;
    //         $roleUser->role_id = $checkRoleExists->id;
    //         $roleUser->public_key = $public_key;
    //         $roleUser->private_key = $private_key;
    //         $roleUser->save();
    //     }
    //     return response()->json(['message' => "Success",'error'=>'false'],200);
    // }
    // Encryption Decryption : END

}
