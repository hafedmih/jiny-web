<?php

namespace App\Transformers\Request;

use App\Models\taxi\Requests\Request as RequestModel;
use League\Fractal\TransformerAbstract;
use App\Models\taxi\Settings;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Requests\RequestPlace;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Driver;
use App\Models\taxi\UserInstantTrip;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\UserComplaint;
use Carbon\Carbon;
use App\Models\taxi\PassengerUploadImages;
use App\Models\User;
use App\Models\taxi\Requests\RequestBill;



class  TripRequestTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'requestBill'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(RequestModel $request)
    {
        

        $request_instant = RequestModel::where('id',$request->id)->where('is_instant_trip',1)->where('is_completed',0)->where('is_cancelled',0)->first();

        if($request_instant)
        {
            $passenger_upload_image = PassengerUploadImages::where('request_id',$request->id)->where('upload','USER')->first();

            if($passenger_upload_image)
            {
                $user_passenger_upload = true;
            }
            else {
                $user_passenger_upload = false;
            }

        }
        else {

            $passenger_upload_image = PassengerUploadImages::where('request_id',$request->id)->where('user_id',$request->user_id)->where('upload','USER')->first();

            if($passenger_upload_image)
            {
                $user_passenger_upload = true;
            }
            else {
                $user_passenger_upload = false;
            }
        }


        $passenger_upload_image = PassengerUploadImages::where('request_id',$request->id)->where('driver_id',$request->driver_id)->where('upload','DRIVER')->first();



        if($passenger_upload_image)
        {
            $driver_passenger_upload = true;
        }
        else {
            $driver_passenger_upload = false;
        }

        if (!empty($request->promo_id)) {
            $promo_applied = true;
        }
        else {
            $promo_applied = false;
        }

        $instant_phone_number = UserInstantTrip::where('request_id',$request->id)->first();

        $driver_details = Driver::where('user_id',$request->driver_id)->first();
        
        $dispute_timing = Settings::where('name','dispute_timing')->first();
        $start_night_time = Settings::where('name','start_night_time')->first();
        $end_night_time = Settings::where('name','end_night_time')->first();

        $dispute_timing = $dispute_timing ? $dispute_timing->value : 0;
        $start_night_time = $start_night_time ? $start_night_time->value : "00:00:00";
        $end_night_time = $end_night_time ? $end_night_time->value : "00:00:00";
        $login_hours = 0 ;
        if($request->trip_start_time != NUll){
          
            $date1 = Carbon::parse($request->trip_start_time); 
            // dd($Carbon::now()->format('Y-m-d H:i:s'));
            $date2 = Carbon::now();
            $login_hours= $date1->diff($date2)->format('%H');
        }

        $share_trip_path = RequestModel::where('id',$request->id)->where('is_completed',0)->where('is_cancelled',0)->where('is_trip_start',1)->first();

        $dispute = UserComplaint::where('request_id',$request->id)->first();
        
        $data = [
            'id'                        => $request->id,
            'request_number'            => $request->request_number,
            'request_otp'               => $request->request_otp,
            'is_later'                  => (int)$request->is_later,
            'user_id'                   => $request->user_id,
            'promo_applied'             => $promo_applied,
            'if_dispatch'               => (int)$request->if_dispatch,
            'trip_start_time'           => $request->trip_start_time,
            'is_driver_started'         => (int)$request->is_driver_started,
            'is_driver_arrived'         => (int)$request->is_driver_arrived,
            'is_trip_start'             => (int)$request->is_trip_start,
            'total_distance'            => $request->total_distance,
            'total_time'                => $request->total_time,
            'is_completed'              => (int)$request->is_completed,
            'is_cancelled'              => (int)$request->is_cancelled,
            'completed_at'              => $request->completed_at,
            'cancelled_at'              => $request->cancelled_at,
            'cancel_method'             => $request->cancel_method,
            'custom_reason'             => $request->custom_reason,
            'payment_opt'               => $request->payment_opt,
            'is_paid'                   => (int) $request->is_paid,
            'user_rated'                => (int)$request->user_rated,
            'driver_rated'              => (int)$request->driver_rated,
            'unit'                      => $request->unit == 0 ? 'KM' : 'KM',
            'zone_type_id'              => $request->zone_type_id,
            'pick_lat'                  => $request->requestPlace ? $request->requestPlace->pick_lat : null,
            'pick_lng'                  => $request->requestPlace ? $request->requestPlace->pick_lng : null,
            'pick_address'              => $request->requestPlace ? $request->requestPlace->pick_address : null,
            'requested_currency_code'   => $request->requested_currency_code,
            'requested_currency_symbol' => $request->requested_currency_symbol,
            'is_instant_trip'           => (int)$request->is_instant_trip,
            'user_overall_rating'       => $request->is_instant_trip == 1 ? 1 : $this->calculateUserRating($request->userDetail->id),
            'driver_overall_rating'     => $request->driverDetail == Null ? Null : $this->calculateUserRating($request->driverDetail->id) ,
            'user'                      => $request->userDetail,
            'driver'                    => $request->driverDetail,
            'dispute_status'            => $dispute ? 0 : ($login_hours < $dispute_timing ? 1 : 0),
            'location_approve'          => $request->location_approve,
            'poly_string'               => $request->requestPlace ? $request->requestPlace->poly_string : '',
            'manual_trip'               => $request->manual_trip,
            'instant_phone_number'      => $instant_phone_number ? $instant_phone_number->phone_number : null,
            'service_category'          => $request->trip_type,
            'driver_notes'              => $request->driver_notes,
            'booking_for'               => $request->booking_for,
            'user_upload_image'         => $user_passenger_upload,
            'driver_upload_image'       => $driver_passenger_upload,
            'skip_night_photo'          => $request->skip ? true : false,
            'start_night_time'          => $start_night_time,
            'end_night_time'            => $end_night_time,

        ];
        

        $passenger_upload_image_user = PassengerUploadImages::where('request_id',$request->id)->where('upload','USER')->first();

        if(!is_null($passenger_upload_image_user))
        $data['night_photo_user'] = $passenger_upload_image_user->images1;

        $passenger_upload_image_driver = PassengerUploadImages::where('request_id',$request->id)->where('driver_id',$request->driver_id)->where('upload','DRIVER')->first();

        if(!is_null($passenger_upload_image_driver))
        $data['night_photo_driver'] = $passenger_upload_image_driver->images1;
      
        if($request->trip_type == 'RENTAL' || $request->trip_type == 'OUTSTATION'){

            $outstation_upload_image = OutstationUploadImages::where('request_id',$request->id)->first();
            if(!is_null($outstation_upload_image))
                $data['trip_start_km_image'] = $outstation_upload_image->trip_start_km_image;

        }

        if($request->trip_type == 'OUTSTATION'){
            $data['trip_end_time'] = $request->trip_end_time;
            $data['outstation_trip_type'] = $request->outstation_trip_type;
        }
        
        if($share_trip_path!='')
        {
            $data['share_path'] = env('APP_URL')."/share-view/".$request->id;
        }
        if($request->booking_for == 'OTHERS'){
            $data['others']['name']         = $request->othersDetail ? $request->othersDetail->firstname :'';
            $data['others']['phone_number'] = $request->othersDetail ? $request->othersDetail->phone_number : '';
        }
            
        $stops = RequestPlace::where('request_id',$request->id)->where('stops',1)->get();
        if(count($stops) >0){
            foreach($stops as $key => $stopsdatat){
                if($stopsdatat->stops == 1){
                    if($key == 0){
                        $data['stops']['address'] = $stopsdatat->stop_address ? $stopsdatat->stop_address : $stopsdatat->drop_address;
                        $data['stops']['latitude'] = $stopsdatat->stop_lat ? $stopsdatat->stop_lat : $stopsdatat->drop_lat;
                        $data['stops']['longitude'] = $stopsdatat->stop_lng ? $stopsdatat->stop_lng : $stopsdatat->drop_lng;
                    }
                }
                $data['drop_lat'] =  $stopsdatat->drop_lat;
                $data['drop_lng'] = $stopsdatat->drop_lng;
                $data['drop_address'] = $stopsdatat->drop_address;     
            }
        }else{
            $data['drop_lat'] =  $request->requestPlace ? $request->requestPlace->drop_lat : null;
            $data['drop_lng'] = $request->requestPlace ? $request->requestPlace->drop_lng : null;
            $data['drop_address'] = $request->requestPlace ? $request->requestPlace->drop_address : null ;
        }

        // Stops Check  

        if($request->driverDetail != Null){
            $data['car_details'] = Driver::where('user_id',$request->driver_id)->first();
        }
        $setting = Settings::where('name','driver_time_out')->first();
        if(is_null($setting)){
           $data['driver_time_out'] = 60;
        
        }else{
            $data['driver_time_out'] = $setting->value ?  $setting->value : 60 ;
        }

        $zonePrice = ZonePrice::find($request->zone_type_id);
       // dd($zonePrice);
        if(!is_null($zonePrice)){
            $type = Vehicle::find($zonePrice->type_id);
           // dd($type);
            $data['grace_waiting_time'] = $zonePrice->ridenow_free_waiting_time;
            $data['grace_waiting_time_after_start'] = $zonePrice->ridenow_free_waiting_time_after_start;
            $data['vehicle_name'] = $type->vehicle_name;
            $data['vehicle_image'] = $type->image;
            $data['vehicle_slug'] = $type->slug;
            $data['vehicle_number'] = $driver_details ? $driver_details->car_number  : '';
            $data['vehicle_model'] = $driver_details ? $driver_details->car_model  : '';
            $data['vehicle_highlight_image'] = $type->highlight_image;

        }
        $packagePrice = PackageItem::find($request->package_item_id);
       // dd($zonePrice);
        if(!is_null($packagePrice)){
         
            $type = Vehicle::find($packagePrice->type_id);
           // dd($type);
            // $data['grace_waiting_time'] = $packagePrice->ridenow_free_waiting_time;
            $data['vehicle_name'] = $type->vehicle_name;
            $data['vehicle_slug'] = $type->slug;
            $data['vehicle_image'] = $type->image;
            $data['vehicle_number'] = $driver_details ? $driver_details->car_number  : '';
            $data['vehicle_model'] = $driver_details ? $driver_details->car_model  : '';
            $data['vehicle_highlight_image'] = $type->highlight_image;
            
                $packagemaster = PackageMaster::where('id',$packagePrice->package_id)->first();
                if(!is_null($packagemaster)){
                    
                    $data['package_name'] = $packagemaster->name;
                    $data['package_hour'] = $packagemaster->hours;
                    $data['package_km'] = $packagemaster->km;
                }
           

            
            

        }
        $outstationkm = OutstationUploadImages::where('request_id',$request->id)->first();
        if(!is_null($outstationkm)){
            $data['start_km'] = $outstationkm->trip_start_km;
            $data['end_km'] = $outstationkm->trip_end_km;
        }
        $outstationPrice = OutstationPriceFixing::find($request->outstation_type_id);
       // dd($zonePrice);
        if(!is_null($outstationPrice)){
            $type = Vehicle::find($outstationPrice->type_id);
           // dd($type);
            // $data['grace_waiting_time'] = $packagePrice->ridenow_free_waiting_time;
            $data['vehicle_name'] = $type->vehicle_name;
            $data['vehicle_slug'] = $type->slug;
            $data['vehicle_image'] = $type->image;
            $data['vehicle_number'] = $driver_details ? $driver_details->car_number  : '';
            $data['vehicle_model'] = $driver_details ? $driver_details->car_model  : '';
            $data['vehicle_highlight_image'] = $type->highlight_image;
            

        }
        return $data;
    }
    
    public function includeRequestBill(RequestModel $request)
    {
        $requestBill = $request->requestBill;

        if($requestBill)
        {
        return $requestBill
        ? $this->item($requestBill, new RequestBillTransformer)
        : $this->null();
        }
    }
    public function calculateUserRating($user_id){
        // dd(RequestRating::where('user_id',$user_id)->avg('rating'));
        return RequestRating::where('user_id',$user_id)->avg('rating');
        
    }
}
