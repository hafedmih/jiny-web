<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\taxi\Requests\RequestBill;

use App\Models\boilerplate\Country;

class OutstationMaster extends Model
{
    use HasFactory;

    protected $table = 'out_station_master';

    protected $fillable = [
         'pick_up','pick_lat','pick_lng','drop','drop_lat','drop_lng','distance','price','status','country','hill_station'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function getCountry() {
        return $this->hasOne(Country::class, 'id', 'country');
    }
    // public function getBaseprice(){
    //     return $this->hasOne(RequestBill::class, 'id','id');
    // }
    // public function basePrice(){
    //     return @this->hasOne(Request::class,)
    // }
}