<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;


class VehicleModel extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'vehicle_model';

    protected $fillable = [  	
        'vehicle_id',
        'image',
        'model_name',   
        'description',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'model_name'
            ]
        ];
    }
   

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return getImage('images/vehiclesmodel',$value);
    }

    public function getVehicle() {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }


}
