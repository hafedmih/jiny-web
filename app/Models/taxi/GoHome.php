<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;


class GoHome extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'go_home';

    protected $fillable = [  	
        'user_id',
        'address',
        'lat',
        'lng',
        'status'
    ];
    
   
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
