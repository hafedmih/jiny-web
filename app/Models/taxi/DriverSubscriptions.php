<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\taxi\Submaster;

class DriverSubscriptions extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "driver_subscriptions";

    protected $fillable = ['user_id','subscription_id','from_date','to_date','amount','paid_status'];

    public function driverDetails()
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function subscriptionDetails()
    {
        return $this->belongsTo(Submaster::class,'subscription_id', 'id');
    }
}
