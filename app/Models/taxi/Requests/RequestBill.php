<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Model;

class RequestBill extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_bills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['request_id','base_price','base_distance','price_per_distance','distance_price','price_per_time','time_price','waiting_charge','cancellation_fee','service_tax','service_tax_percentage','promo_discount','admin_commision','driver_commision','total_amount','requested_currency_code','admin_commision_with_tax','total_distance','total_time','requested_currency_symbol','sub_total','out_of_zone_price','booking_fees','hill_station_price','package_hours','package_km','pending_km'];

    public function requestDetail()
    {
        return $this->belongsTo(Request::class, 'request_id', 'id');
    }
}