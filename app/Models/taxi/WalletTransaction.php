<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\taxi\Requests\Request;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;


class WalletTransaction extends Model
{
    use HasFactory;
    protected $table = 'wallet_transaction';

    protected $fillable = [  	
        'wallet_id',
        'request_id',
        'amount',
        'purpose',
        'type',
        'user_id',
    ];

// public function getWalletTransaction()
//     {
//         return $this->hasMany(WalletTransaction::class, 'wallet_id', 'id');
//     }

    public function getRequest()
    {
        return $this->hasOne(Request::class, 'id', 'request_id'); 
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id'); 
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
