<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZonePrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "zone_price";

    protected $fillable = ['zone_id','type_id','ridenow_base_price','ridenow_price_per_time','ridenow_base_distance','ridenow_price_per_distance','ridenow_free_waiting_time','ridenow_free_waiting_time_after_start','ridenow_waiting_charge','ridenow_cancellation_fee','ridelater_base_price','ridelater_price_per_time','ridelater_base_distance','ridelater_price_per_distance','ridelater_free_waiting_time','ridelater_free_waiting_time_after_start','ridelater_waiting_charge','ridelater_cancellation_fee','status','slug','ridenow_admin_commission_type','ridenow_admin_commission','ridelater_admin_commission_type','ridelater_admin_commission','ridenow_booking_base_fare','ridenow_booking_base_per_kilometer','ridelater_booking_base_fare','ridelater_booking_base_per_kilometer'];

    public function getSurgePrice()
    {
        return $this->hasMany(ZoneTypeSurgePrice::class, 'zone_type_id', 'id');
    }

    public function getZone()
    {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }

    public function getType()
    {
        return $this->hasOne(Vehicle::class, 'id', 'type_id');
    }

}
