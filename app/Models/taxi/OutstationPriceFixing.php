<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutstationPriceFixing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'outstation_price_fixing';

    protected $fillable = [  	
        'type_id',
        'distance_price',
        'distance_price_two_way',
        'admin_commission',
        'admin_commission_type',
        'driver_price',
        'grace_time',
        'hill_station_price',
        'waiting_charge', 
        'day_rent_two_way', 
        'base_fare', 
        'minimum_km', 
        'status', 
        'status'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function getVehicle() {
        return $this->hasOne(Vehicle::class, 'id', 'type_id');
    }
}
