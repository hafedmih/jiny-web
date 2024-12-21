<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutstationUploadImages extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'outstation_upload_images';

    protected $fillable = [  	
        'request_id',
        'user_id',
        'trip_start_km_image',
        'trip_start_km',
        'trip_end_km_image',
        'trip_end_km',
        'distance',
        'status'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function getVehicle() {
        return $this->hasOne(Vehicle::class, 'id', 'type_id');
    }

    public function getTripStartKmImageAttribute($value)
    {
        
        if (empty($value)) {
            return null;
        }

        return getImage('images/outstation',$value);
    }

    public function getTripEndKmImageAttribute($value)
    {
        
        if (empty($value)) {
            return null;
        }

        return getImage('images/outstation',$value);
    }
}
