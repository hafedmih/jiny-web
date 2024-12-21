<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class DriverLogs extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "driver_logs";

    protected $fillable = ['driver_id','date','online_time','offline_time','working_time','status'];
    
    public function getDriverDetails()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
}
