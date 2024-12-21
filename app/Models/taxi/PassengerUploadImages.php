<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassengerUploadImages extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'passenger_upload_images';

    protected $fillable = [  	
        'request_id',
        'driver_id',
        'user_id',
        'image',
        'upload',
        'upload_time',
        'status',
        'user_upload_image',
        'driver_upload_image'
    ];

    protected $appends = ['images1'];
    

    public function getImages1Attribute()
    {
        $value = $this->image;
        if (empty($value)) {
            return null;
        }

        return getImage('images/passengers',$value);
    }
}
