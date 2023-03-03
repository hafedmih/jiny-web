<?php

use App\Models\taxi\Driver;
use App\Models\taxi\Requests\Request;
use App\Models\taxi\Requests\RequestMeta;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Models\taxi\Settings;
/**
 * Upload images
 * @param $uploadPath Path to store file
 * @param $image File to upload
 * @param optional $uploadedFile Already stored in db
 * 
 * @return $filename Hashname of the file
*/
if (!function_exists('uploadImage')) 
{
    function uploadImage(string $uploadPath,$image,$uploadedFile = null,$separator = '/')
    {
        // Delete file if exists
        if(Storage::exists($uploadPath.$separator.$uploadedFile)){
            Storage::delete($uploadPath.$separator.$uploadedFile);
        }

        Storage::put('public/'.$uploadPath,$image);

        $filename = $image->hashName();

        return $filename;
    }
}

/**
 * Get uploaded image
 * @param $filePath Path of the file
 * @param $filename File to retrieve
 * 
 * @return $path full qualified path to get image
*/
if (!function_exists('getImage')) 
{
    function getImage($filePath,$filename,$separator = '/')
    {
        return env('IMAGE_URL').Storage::url($filePath.$separator.$filename);
    //    return Storage::url($filePath.$separator.$filename);
    }
}


/**
 * Delete image
 * @param $filePath Path of the file
 * @param $filename File to retrieve
 * 
 * @return $path full qualified path to get image
*/
if (!function_exists('deleteImage')) 
{
    function deleteImage($filePath,$filename,$separator = '/')
    {
        if(Storage::exists($filePath.$separator.$filename)){
            Storage::delete($filePath.$separator.$filename);
        }

        return true;
        
    }
}

/**
 *  Emit send event and listen on client side
 * @param data object contains event and message
 * event is unique per user i.e, event_1
 * publish event from laravel app and subscribe in node with same channel name, ex: general
 * $data = ['event' => 'event_1','message' => ['data']] | 1 is userid here
 * sendSocketData($data)
*/
if (!function_exists('sendSocketData'))
{
    function sendSocketData($data)
    {
        Redis::publish('general',json_encode($data));
    }
}

/**
 * fetch drivers node from firebase database
 * @param $lat pickup-latitude
 * @param $lng pickup-longitude
 * @param $type vehicle type
 * 
 * @return json available drivers data
*/
if (!function_exists('fetchDrivers'))
{
    function fetchDrivers($lat = 11.01511066262459, $lng = 76.98246916575717, $type = 1,$ride_type = 'LOCAL')
    {
        try {
            // dd($ride_type);
            $radius = 4;
           
            $search_radius = Settings::where('name','driver_search_radius')->first();
            if(is_null($search_radius)){
                $radius = 4;
               
            }
            if($search_radius->value == null){
                $radius = 4;
            }else{
                $radius = $search_radius->value;
            }

      
            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);
    
            $url = "/{$lat}/{$lng}/{$type}/{$ride_type}/{$radius}";

            $result = $client->get($url,[
                'timeout' => 15,
                'connect_timeout' => 5
            ]);
            
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());
              
                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
                // $data1 = collect($data)->sort('distance');
                
                // $count = count($data);
                // echo($data );
                // array_multisort($count, SORT_ASC, $zone_price);
                // $data = ($data)->sortBy('distance');
                // dd($data);
                //@TODO check drivers data with local db
                $geoDrivers = [];
                foreach($data->data as $k => $driver) {
                    $geoDrivers[$k]['id'] = $driver->id;
                    $geoDrivers[$k]['distance'] = $driver->distance;
                    $geoDrivers[$k]['first_name'] = $driver->first_name;
                    $geoDrivers[$k]['g'] = $driver->g;
                    $geoDrivers[$k]['is_active'] = $driver->is_active;
                    $geoDrivers[$k]['is_available'] = $driver->is_available;
                    $geoDrivers[$k]['last_name'] = $driver->last_name;
                    $geoDrivers[$k]['phone_number'] = $driver->phone_number;
                    //$geoDrivers[$k]['service_category'] = $driver->service_category;
                    $geoDrivers[$k]['type'] = $driver->type;
                    $geoDrivers[$k]['updated_at'] = $driver->updated_at;
                    
                    // if (count($geoDrivers) == 5) break;
                }

                $geoDrivers = collect($geoDrivers)->sortBy('distance');

                // dd($geoDrivers);
                $driverId = $geoDrivers->pluck('id')->toArray();

                $metaDrivers = RequestMeta::pluck('driver_id')->toArray();

                $drivers = Driver::whereIsActive(true)->whereIsApprove(true)->whereIsAvailable(true)->whereNotIn('id', $metaDrivers)
                                    ->whereHas('users',function($q) use ($driverId){
                                        $q->whereIn('slug', $driverId)->where('active',1);
                                    })
                                    ->get();
                // dd($drivers);
                // if (count($drivers) < 0) {
                //     return response()->json(['success' => false,'data' => 'No driver found']);
                // }
                // dd($data);
                return response()->json(['success' => true, 'data' => $geoDrivers]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found1']);
            }
        } catch (\Throwable $th) {
            // dd($th);
            return response()->json(['success' => false,'data' => 'No driver found2']);
        }
    }
}

if (!function_exists('fetchDriversRadius'))
{
    function fetchDriversRadius($lat = 11.01511066262459, $lng = 76.98246916575717, $type = 1,$ride_type = 'LOCAL',$radius = 500)
    {
        try {

            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);

            $url = "/{$lat}/{$lng}/{$type}/{$ride_type}/{$radius}";

            $result = $client->get($url,[
                'timeout' => 15,
                'connect_timeout' => 5
            ]);
            
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());
            
                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
                // $data1 = collect($data)->sort('distance');
                
                // $count = count($data);
                // echo($data );
                // array_multisort($count, SORT_ASC, $zone_price);
                // $data = ($data)->sortBy('distance');
                // dd($data);
                //@TODO check drivers data with local db
                $geoDrivers = [];
                foreach($data->data as $k => $driver) {
                    $geoDrivers[$k]['id'] = $driver->id;
                    $geoDrivers[$k]['distance'] = $driver->distance;
                    $geoDrivers[$k]['first_name'] = $driver->first_name;
                    $geoDrivers[$k]['g'] = $driver->g;
                    $geoDrivers[$k]['is_active'] = $driver->is_active;
                    $geoDrivers[$k]['is_available'] = $driver->is_available;
                    $geoDrivers[$k]['last_name'] = $driver->last_name;
                    $geoDrivers[$k]['phone_number'] = $driver->phone_number;
                    //$geoDrivers[$k]['service_category'] = $driver->service_category;
                    $geoDrivers[$k]['type'] = $driver->type;
                    $geoDrivers[$k]['updated_at'] = $driver->updated_at;
                    
                    // if (count($geoDrivers) == 5) break;
                }

                $geoDrivers = collect($geoDrivers)->sortBy('distance');

                // dd($geoDrivers);
                $driverId = $geoDrivers->pluck('id')->toArray();

                $metaDrivers = RequestMeta::pluck('driver_id')->toArray();

                $drivers = Driver::whereIsActive(true)->whereIsApprove(true)->whereIsAvailable(true)->whereNotIn('id', $metaDrivers)
                                    ->whereHas('users',function($q) use ($driverId){
                                        $q->whereIn('slug', $driverId)->where('active',1);
                                    })
                                    ->get();
                // dd($drivers);
                // if (count($drivers) < 0) {
                //     return response()->json(['success' => false,'data' => 'No driver found']);
                // }
                // dd($data);
                return response()->json(['success' => true, 'data' => $geoDrivers]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found1']);
            }
        } catch (\Throwable $th) {
            // dd($th);
            return response()->json(['success' => false,'data' => 'No driver found2']);
        }
    }
}

if (!function_exists('fetchDriversNotUpdated'))
{
    function fetchDriversNotUpdated($lat = 11.0116775, $lng = 76.8271451)
    {
        try {
            $radius = 100;
        
            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);
    
            $url = "get-drivers-not-updated/{$lat}/{$lng}/{$radius}";

            $result = $client->get($url,[
                'timeout' => 15,
                'connect_timeout' => 5
            ]);
            
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());
              
                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
                // $data1 = collect($data)->sort('distance');
                
                // $count = count($data);
                // echo($data );
                // array_multisort($count, SORT_ASC, $zone_price);
                // $data = ($data)->sortBy('distance');
                // dd($data);
                //@TODO check drivers data with local db
                $geoDrivers = [];
                foreach($data->data as $k => $driver) {
                    $geoDrivers[$k]['id'] = $driver->id;
                    $geoDrivers[$k]['distance'] = $driver->distance;
                    $geoDrivers[$k]['first_name'] = $driver->first_name;
                    $geoDrivers[$k]['g'] = $driver->g;
                    $geoDrivers[$k]['is_active'] = $driver->is_active;
                    $geoDrivers[$k]['is_available'] = $driver->is_available;
                    $geoDrivers[$k]['last_name'] = $driver->last_name;
                    $geoDrivers[$k]['phone_number'] = property_exists($driver, 'phone_number') ? $driver->phone_number : '';
                   // $geoDrivers[$k]['service_category'] = $driver->service_category;
                    $geoDrivers[$k]['type'] = $driver->type;
                    $geoDrivers[$k]['updated_at'] = $driver->updated_at;
                    
                    // if (count($geoDrivers) == 5) break;
                }

                $geoDrivers = collect($geoDrivers)->sortBy('distance');

                // dd($geoDrivers);
                $driverId = $geoDrivers->pluck('id')->toArray();

                $metaDrivers = RequestMeta::pluck('driver_id')->toArray();

                $drivers = Driver::whereIsActive(true)->whereIsApprove(true)->whereIsAvailable(true)->whereNotIn('id', $metaDrivers)
                                    ->whereHas('users',function($q) use ($driverId){
                                        $q->whereIn('slug', $driverId)->where('active',1);
                                    })
                                    ->get();
                // dd($drivers);
                // if (count($drivers) < 0) {
                //     return response()->json(['success' => false,'data' => 'No driver found']);
                // }
                // dd($data);
                return response()->json(['success' => true, 'data' => $geoDrivers]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found1']);
            }
        } catch (\Throwable $th) {
            dd($th);
            return response()->json(['success' => false,'data' => 'No driver found2']);
        }
    }
}

if (!function_exists('fetchDriversLogout'))
{
    function fetchDriversLogout()
    {
        try {
            $lat = 11.0116775;
            $lng = 76.8271451;
            $radius = 100;
        
            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);
    
            $url = "drivers-logout/{$lat}/{$lng}/{$radius}";

            $result = $client->get($url,[
                'timeout' => 15,
                'connect_timeout' => 5
            ]);
            
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());
              
                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
               
                return response()->json(['success' => true, 'data' => $data]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found1']);
            }
        } catch (\Throwable $th) {
            dd($th);
            return response()->json(['success' => false,'data' => 'No driver found2']);
        }
    }
}

if (!function_exists('fetchDriverDistance'))
{
    function fetchDriverDistance()
    {
        try {
            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);
    
            $url = "/slug";

            $result = $client->get($url,[
                'timeout' => 5,
                'connect_timeout' => 5
            ]);
           // dd($result->getStatusCode());
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());

                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
    
                //@TODO check drivers data with local db
                $geoDrivers = [];
                foreach($data->data as $k => $driver) {
                    $geoDrivers[$k]['id'] = $driver->id;
                    $geoDrivers[$k]['distance'] = $driver->distance;
                    
                    if (count($geoDrivers) == 5) break;
                }

                $geoDrivers = collect($geoDrivers)->sortBy('distance');
                $driverId = $geoDrivers->pluck('id')->toArray();

                $metaDrivers = RequestMeta::pluck('driver_id')->toArray();

                $drivers = Driver::whereIsActive(true)->whereIsApprove(true)->whereIsAvailable(true)->whereNotIn('id', $metaDrivers)
                                    ->whereHas('users',function($q) use ($driverId){
                                        $q->whereIn('slug', $driverId);
                                    })
                                    ->get();
                
                if (count($drivers) < 0) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }

                return response()->json(['success' => true, 'data' => $data->data]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found']);
            }
        } catch (\Throwable $th) {
           // dd($th);
            return response()->json(['success' => false,'data' => 'No driver found']);
        }
    }
}
if (!function_exists('getNotUpdatedDrivers'))
{
    function getNotUpdatedDrivers()
    {
        try {
            $client = new Client([
                'base_uri' => env('NODE_GEOFIRE_URL','http://localhost') .':'. env('NODE_GEOFIRE_PORT',4000)
            ]);
    
            $url = "/{$lat}/{$lng}/{$type}";

            $result = $client->get($url,[
                'timeout' => 5,
                'connect_timeout' => 5
            ]);
            
            if ($result->getStatusCode() == 200) {
                $data = json_decode($result->getBody()->getContents());

                if (!$data->data) {
                    return response()->json(['success' => false,'data' => 'No driver found']);
                }
    
                return response()->json(['success' => true, 'data' => $data->data]);
            }else{
                return response()->json(['success' => false,'data' => 'No driver found']);
            }
        } catch (\Throwable $th) {
            // dd($th);
            return response()->json(['success' => false,'data' => 'No driver found']);
        }
    }
}

/**
 * generate request number 
 * get request_number from requests model then increment +1 with that
 * 
*/
if (!function_exists('generateRequestNumber')) 
{
    function generateRequestNumber()
    {
        $requestModel = Request::latest()->first();
        if ($requestModel) {
            $requestNumber = explode('_', $requestModel->request_number);
            $lastIndex = $requestNumber[1];
        }else{
            $lastIndex = 0;
        }

        $index = (string) $lastIndex + 1;

        return 'TAXI_'.sprintf("%06d", $index);
    }
}


/**
 * Find distance between two coordinates 
 * 
*/
if(!function_exists('distanceBetweenTwoPoints')){
    function distanceBetweenTwoPoints($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $speed = 50;
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
      
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
      
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        $distInMeter = ($angle * $earthRadius);

        return (double) $distInMeter;
    }
}


/**
 * Get setting table value
 * 
*/
if(!function_exists('settingValue')){
    function settingValue($name)
    {
        $setting = Settings::where('name',$name)->first();

        if($name == 'logo' || $name == 'mini_logo'){
            return $setting ? $setting->Image : '';
        }

        return $setting ? $setting->value : '';
    }
}