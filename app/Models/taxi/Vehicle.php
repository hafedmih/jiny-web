<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;


class Vehicle extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'vehicle';

    protected $appends = ['service_types'];

    protected $fillable = [  	
        'vehicle_name',
        'image',
        'highlight_image',
        'capacity',   
        'status',
        'slug',
        'category_id',
        'service_type',
        'sorting_order',
        'types_id'
    ];

    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'vehicle_name'
            ]
        ];
    }

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return getImage('images/vehicles',$value);
    }


    public function getHighlightImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return getImage('images/vehicles',$value);
    }

    public function getServiceTypesAttribute()
    {
        $value = $this->service_type;
        if (empty($value)) {
            return null;
        }
        return explode(',',$value);
    }

    public function getCategory() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function getModel() {
        return $this->hasOne(VehicleModel::class, 'vehicle_id', 'id');
    }

    public function getOutstationPrice() {
        return $this->hasOne(OutstationPriceFixing::class, 'type_id', 'id');
    }
}
