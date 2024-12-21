<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;


class Favourite extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    
    protected $table = 'favourite_place';

    protected $fillable = [  	
        'title',
        'user_id',
        'latitude',
        'longitude',
        'address',
        'status',
        'slug'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }



public function getCreatedAtAttribute($value)
{
    $dt = new DateTime($value);
    $tz = new DateTimeZone('Asia/Kolkata'); // or whatever zone you're after
    $dt->setTimezone($tz);
    return $dt->format('Y-m-d H:i:s');


}

public function getUpdatedAtAttribute($value)
{
    $dt = new DateTime($value);
    $tz = new DateTimeZone('Asia/Kolkata'); // or whatever zone you're after
    $dt->setTimezone($tz);
    return $dt->format('Y-m-d H:i:s');
}


}
