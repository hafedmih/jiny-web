<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
// use App\Models\taxi\Requests\RequestBill;

// use App\Models\boilerplate\Country;

class OutstationPackage extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'out_station_package';

    protected $fillable = [
         'id','slug','base_price','driver_bata','price_per_km','hours','package_name','vehicle_type','status'
    ];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'package_name'
            ]
        ];
    }

    protected $hidden = ['created_at','updated_at','deleted_at'];

    // public function getCountry() {
    //     return $this->hasOne(Country::class, 'id', 'country');
    // }
    // public function getBaseprice(){
    //     return $this->hasOne(RequestBill::class, 'id','id');
    // }
    // public function basePrice(){
    //     return @this->hasOne(Request::class,)
    // }

    public function getVehicletype(){
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_type');
    }
}