<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\boilerplate\Country;

class PackageMaster extends Model
{
    use HasFactory,Sluggable;
    protected $table = 'package_master';

    protected $fillable = [  	
        'id',
        'hours',
        'km',
        'name',
        'country',
        'admin_commission_type',
        'admin_commission',
        'driver_price',
        'time_cast_type',
        'is_base_package',
        'slug',
        'status'      
    ];

    protected $appends = ['time_cost_type'];

    protected $hidden = ['created_at','updated_at','deleted_at','time_cast_type'];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getTimeCostAttribute($value)
    {
        $value = $this->time_cast;
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    public function getTimeCostTypeAttribute($value)
    {
        $value = $this->time_cast_type;
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    public function getDistanceCostAttribute($value)
    {
        $value = $this->distance_cast;
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    public function getPackageItems() {
        return $this->hasMany(PackageItem::class, 'package_id', 'id');
    }

    public function getCountry() {
        return $this->hasOne(Country::class, 'id', 'country');
    } 

}
