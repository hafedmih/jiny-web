<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripLogs extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'trip_logs';

    protected $fillable = [
        'user_id',
        'driver_id',
        'request_id',
        'type',
        'status'
    ];
    
    public function userDetails()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
}
