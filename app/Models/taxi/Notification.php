<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Notification extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    
    protected $table = 'notification';

    protected $fillable = [  	
        'title',
        'driver_id',
        'user_id',
        'sub_title',
        'message',
        'has_redirect_url',
        'redirect_url',
        'image1',
        'image2',
        'image3',
        'slug',
        'date',
        'notification_type',
        'status'
    ];

    protected $appends = ['images1','images2','images3'];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function getImages1Attribute()
    {
        $value = $this->image1;
        if (empty($value)) {
            return null;
        }

        return getImage('images/notification',$value);
    }

    public function getImages2Attribute()
    {
        $value = $this->image2;
        if (empty($value)) {
            return null;
        }

        return getImage('images/notification',$value);
    }

    public function getImages3Attribute()
    {
        $value = $this->image3;
        if (empty($value)) {
            return null;
        }

        return getImage('images/notification',$value);
    }

    public function getDateAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return date("d-m-Y h:i:s A",strtotime($value));
    }
}
