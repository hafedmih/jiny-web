<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\taxi\Requests\Request;

class CancellationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reason', 'request_id', 'cancellation_fee', 'is_paid', 'custom_reason', 'cancelled_by', 'cancel_type', 'user_lat', 'user_lng', 'driver_lat', 'driver_lng', 'user_location', 'driver_location', 'status','distance'
    ];

    public function requestDetails() {
        return $this->hasOne(Request::class, 'id', 'request_id');
    }

    public function resonDetails() {
        return $this->hasOne(CancellationReason::class, 'id', 'reason');
    }
}