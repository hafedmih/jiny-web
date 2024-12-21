<?php

namespace App\Http\Controllers\boilerplate\Web\RolePermission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Http\Requests;
use Validator;
use Auth;
use DB;
use  App\Models\boilerplate\RolePermission\Permission;



class PermissionsController extends Controller
{
   public function __construct()
    {
        $this->middleware('permission:list-permission', ['only' => ['index','show']]);
        $this->middleware('permission:add-new-permission', ['only' => ['create','store']]);
        $this->middleware('permission:edit-permission', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-permission', ['only' => ['destroy']]);
    }
    public function index(Request $request){   
        $user = Auth::user();
        $user->assignRole('Super Admin');      
        $permissionList = Permission::get();
        // $permissionList->appends($request->except('page'));
        return view('boilerplate.roles-permission.permissions.permissionList', ['permissionList' => $permissionList]);
    }


    public function store(Request $request){ 
        
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|max:255|unique:permissions',
            'display_name' => 'required|max:255|unique:permissions',
            'category' => 'required|min:3|max:255'
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
        try{
            $permissions = new Permission();
            $permissions->name = strip_tags(trim($request->input('name')));
            $permissions->display_name = strip_tags(trim($request->input('display_name')));
            $permissions->description = strip_tags(trim($request->input('description')));
            $permissions->category = strip_tags(trim($request->input('category')));
            $permissions->guard_name = 'web'; 
            // $permissions->visible_for_admin = strip_tags(trim($request->input('visible_to_admin')));
            $permissions->save();
            return response()->json(['message' =>'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' =>'failure.'.$e], 400); 
        }
        return back();
    }

    public function edit($slug){ 

        $permission = Permission::where('slug', $slug)->first();
        if(!is_null($permission)){
            return response()->json(['message' =>'success','data' => $permission], 200);
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        } 
    }

    public function update(Request $request, $slug){ 
       
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|max:255',
            'display_name' => 'required|max:255',
            'category' => 'required|min:3|max:255'
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $permission = Permission::where('slug', $slug)->first();

        if(!is_null($permission)){
            try{
                $permission->name = strip_tags(trim($request->input('name')));
                $permission->display_name = strip_tags(trim($request->input('display_name')));
                $permission->description = strip_tags(trim($request->input('description')));
                $permission->category = strip_tags(trim($request->input('category')));
                // $permission->visible_for_admin = strip_tags(trim($request->input('visible_to_admin')));
                $permission->save();
                return response()->json(['message' =>'success'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' =>'failure.'.$e], 400); 
            }  
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        }  
        return back();          
    }

    public function destroy($slug){ 
    
        $permissions = Permission::where('slug', $slug)->first();
        $permdelete = DB::table('role_has_permissions')->where('permission_id',$permissions->id)->count();
        if($permdelete > 0){
            session()->flash('message',"Permission assigned to User,so cannot be deleted");
            return back();
        }
        $permissions = Permission::where('slug', $slug)->delete();
        return back();
    }
        // if(!is_null($permissions)){
        //     try{
        //         $permissions->delete();
        //         return back();
        //     } catch (\Exception $e) {
        //         return response()->json(['message' =>'failure.'.$e], 400); 
        //     }
        // }else{
        //     return response()->json(['message' =>'failure.'], 404);
        // }
        // return back();
    // }
}
