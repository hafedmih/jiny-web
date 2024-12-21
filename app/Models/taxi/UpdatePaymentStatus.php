<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\taxi\Requests\Request;
use App\Models\User;

class UpdatePaymentStatus extends Model
{
    use HasFactory;

    protected $table = 'update_payment_status';

    protected $fillable = [
        'user_id',
        'request_id',
        'payment_id',
        'is_paid',
        'amount'
    ];

    public function requestDetail()
    {
        return $this->hasOne(Request::class, 'id', 'request_id');
    }

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
