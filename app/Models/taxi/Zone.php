<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\boilerplate\Country;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;


class Zone extends Model
{
    use HasFactory, SoftDeletes, Sluggable,SpatialTrait;

    protected $table = "zone";

    protected $spatialFields = [
        'map_zone'
    ];

    protected $fillable = ['zone_name','primary_zone_id','country','admin_commission_type','admin_commission','map_zone','payment_types','unit','non_service_zone','slug','status','types_id','map_cooder','zone_level','driver_assign_method'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'zone_name'
            ]
        ];
    }

    public function getCountry()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function getZonePrice()
    {
        return $this->hasMany(ZonePrice::class, 'zone_id', 'id');
    }

    public function getDriver() {
        return $this->hasMany(Driver::class, 'service_location', 'id');
    }

    public function getPrimaryZone() {
        return $this->hasOne(Zone::class, 'id', 'primary_zone_id');
    }

}