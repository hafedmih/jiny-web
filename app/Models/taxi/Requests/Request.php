<?php

namespace App\Models\taxi\Requests;

use Alsofronie\Uuid\UuidModelTrait;
use App\Models\taxi\CancellationRequest;
use App\Models\taxi\Driver;
use App\Models\User;
use App\Models\taxi\Customer;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\OutstationMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\UserInstantTrip;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\PackageItem;
use App\Models\taxi\RequestQuestions;
use Carbon\Carbon;
use App\Models\taxi\PassengerUploadImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DateTime;
use DateTimeZone;

class Request extends Model
{
    use HasFactory;
    use UuidModelTrait;

    protected $table = 'requests';

    protected $fillable = [
        'request_number','is_later','user_id','driver_id','trip_start_time','arrived_at','accepted_at','completed_at','cancelled_at','is_driver_started','is_driver_arrived','is_trip_start','is_completed','is_cancelled','cancel_method','total_distance','total_time','payment_opt','is_paid','user_rated','driver_rated','timezone','unit','if_dispatch','zone_type_id','requested_currency_code','requested_currency_symbol','custom_reason','attempt_for_schedule','request_otp','dispatcher_id','driver_notes','is_instant_trip','promo_id','location_approve','ride_type','hold_status','availables_status','trip_type','rental_package','manual_trip','outstation_id','outstation_type_id','package_id','package_item_id','booking_for','others_user_id','created_by','outstation_trip_type','trip_end_time','destination_type','amount'
    ];

    public function requestPlace()
    {
        return $this->hasOne(RequestPlace::class, 'request_id', 'id');
    }
    public function othersDetail()
    {
        return $this->hasOne(User::class, 'others_user_id', 'id');
    }
    public function requestStops()
    {
        return $this->hasMany(RequestPlace::class, 'request_id', 'id');
    }

    public function requestHistory()
    {
        return $this->hasOne(RequestHistory::class, 'request_id', 'id');
    }

    public function outstationDetails()
    {
        return $this->belongsTo(OutstationMaster::class, 'outstation_id', 'id');
    }

    public function outstationPriceDetails()
    {
        return $this->belongsTo(OutstationPriceFixing::class, 'outstation_type_id', 'id');
    }

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userInstantDetail()
    {
        return $this->belongsTo(UserInstantTrip::class, 'user_id', 'id');
    }

    public function driverDetail()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function requestMeta()
    {
        return $this->hasMany(RequestMeta::class, 'request_id', 'id');
    }

    public function cancellationRequest()
    {
        return $this->hasOne(CancellationRequest::class,'request_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    public function requestBill()
    {
        return $this->hasOne(RequestBill::class, 'request_id', 'id');
    }

    public function getZonePrice()
    {
        return $this->hasOne(ZonePrice::class, 'id', 'zone_type_id');
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, 'request_id', 'id');
    }

    public function getAssignAmount()
    {
        return $this->hasOne(RequestSetAmount::class, 'request_id', 'id');
    }

    public function getPassengerUploadImage()
    {
        return $this->hasMany(PassengerUploadImages::class, 'request_id', 'id');
    }

    public function getWalletRequest()
    {
        return $this->hasMany(WalletTransaction::class, 'request_id', 'id');
    }

    public function getPackage()
    {
        return $this->hasOne(PackageMaster::class, 'id', 'package_id');
    }

    public function getPackageItem()
    {
        return $this->hasOne(PackageItem::class, 'id', 'package_item_id');
    }

    

   


    public function getCreatedAtAttribute($value)
    {

        if(!$value){
           return false;
        }
        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('Y-m-d H:i:s');
    }



    public function getUpdatedAtAttribute($value)
    {
        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('Y-m-d H:i:s');
    }


    public function getCompletedAtAttribute($value)
    {

        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }

    public function getCancelledAtAttribute($value)
    {
        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }


    public function getTripStartTimeAttribute($value)
    {

        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }

    public function getTripEndTimeAttribute($value)
    {

        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }

    public function getArrivedAtAttribute($value)
    {
        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }

    public function getAcceptedAtAttribute($value)
    {
        if(!$value){
            return false;
         }

        $dt = new DateTime($value);
        $tz = new DateTimeZone('Africa/Nouakchott'); // or whatever zone you're after
        $dt->setTimezone($tz);
        return $dt->format('d-m-Y H:i:s');
    }





}


