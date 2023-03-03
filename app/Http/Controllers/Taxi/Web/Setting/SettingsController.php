<?php

namespace App\Http\Controllers\Taxi\Web\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\Settings;
use App\Models\boilerplate\Country;
use App\Models\boilerplate\Languages;

class SettingsController extends Controller
{
    public function settings(Request $request)
    {
        $setting = Settings::where('status',1)->get();
        $time_zone = Country::get();
        $languages = Languages::get();
        $data = [];
        foreach ($setting as $value) {
            $data[$value->name] = $value->image ? $value->image : $value->value;
        }
        // $data = (object)$data;
        return view('taxi.settings.index',['settings' => $data,'time_zone' => $time_zone,'languages' => $languages]);
    }

    public function settingsSave(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            $type = "TEXT";

            $setting = Settings::where('name',$key)->first();
            if($request->file($key)){
                if($setting){
                    deleteImage('images/settings',$setting->value);
                }
                $value =  uploadImage('images/settings',$request->file($key));
                $type = "FILE";
            }
            if($value == null && $key == "logo" || $value == null && $key == "mini_logo"){
                $value = $setting ? $setting->value : NULL;
                $type = "FILE";
            }
            
            if($setting){
                $setting->value = $value;
                $setting->type = $type;
                $setting->save();
            }
            else{
                Settings::create([
                    'name' => $key,
                    'value' => $value,
                    'status' => 1,
                    'type' => $type
                ]);
            }
        }

        return response()->json(['message' =>'success'], 200);
    }
}
