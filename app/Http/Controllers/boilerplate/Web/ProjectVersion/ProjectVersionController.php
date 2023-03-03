<?php

namespace App\Http\Controllers\boilerplate\Web\ProjectVersion;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\boilerplate\ProjectVersion;

class ProjectVersionController extends Controller
{

     function __construct()
    {
        $this->middleware('permission:list-version', ['only' => ['index']]);
        $this->middleware('permission:add-new-version', ['only' => ['store']]);
        $this->middleware('permission:edit-version', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-version', ['only' => ['destroy']]);
    }


    public function index(Request $request){         
        $versionList = ProjectVersion::search($request->get('query', ''))->orderBy('id', 'DESC')->get();
        // $versionList->appends($request->except('page'));
        return view('boilerplate.project-version.project-version', ['versionList' => $versionList]);
    }

    public function getVersionCode(){
        $versionCode = 'V-'.hash('md2',Carbon::now()); 
        return $versionCode;
    }
    public function store(Request $request){ 
        $validator = Validator::make($request->all(),
        [
            'version_number' => 'required',
            'description' => 'required',
            'application_type' => 'required',
            'version_code' => 'required'
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
        try{
            $versions = new ProjectVersion();
            $versions->version_number = $request->input('version_number');
            $versions->description = $request->input('description');
            $versions->application_type = $request->input('application_type');
            $versions->version_code = $request->input('version_code');
            $versions->save();
            return response()->json(['message' =>'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' =>'failure.'.$e], 400); 
        }
        return back();
    }

    public function edit($slug){ 

        $version = ProjectVersion::where('slug', $slug)->first();
        if(!is_null($version)){
            return response()->json(['message' =>'success','data' => $version], 200);
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        } 
    }

    public function update(Request $request, $slug){ 
       
        $validator = Validator::make($request->all(),[
            'version_number' => 'required',
            'description' => 'required',
            'application_type' => 'required',
            'version_code' => 'required'
        ]);  
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $version = ProjectVersion::where('slug', $slug)->first();

        if(!is_null($version)){
            try{
                $version->version_number = strip_tags(trim($request->input('version_number')));
                $version->description = strip_tags(trim($request->input('description')));
                $version->application_type = strip_tags(trim($request->input('application_type')));
                $version->version_code = strip_tags(trim($request->input('version_code')));
                // $permission->visible_for_admin = strip_tags(trim($request->input('visible_to_admin')));
                $version->save();
                return response()->json(['message' =>'success'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' =>'failure.'.$e], 400); 
            }  
        }else{
            return response()->json(['message' =>'failure.'.$e], 404);
        }  
        return back();          
    }

    // public function destroy($slug){ 
    
    //     $versions = ProjectVersion::where('slug', $slug)->first();
    //     if(!is_null($versions)){
    //         try{
    //             $versions->delete();
    //             return back();
    //         } catch (\Exception $e) {
    //             return response()->json(['message' =>'failure.'.$e], 400); 
    //         }
    //     }else{
    //         return response()->json(['message' =>'failure.'], 404);
    //     }
    //     return back();
    // }

    public function banned($slug){ 
    
        $versions = ProjectVersion::where('slug', $slug)->first();
        if(!is_null($versions)){
            try{
                if($versions->status == "OPEN")
                {
                    $versions->status = "CLOSE";
                }
                else{
                    $versions->status = "OPEN";
                }
                $versions->save();
                return back();
            } catch (\Exception $e) {
                return response()->json(['message' =>'failure.'.$e], 400); 
            }
        }else{
            return response()->json(['message' =>'failure.'], 404);
        }
        return back();
    }
}
