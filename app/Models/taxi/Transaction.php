<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\taxi\Requests\Request;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    protected $fillable = [
        'user_id',
        'request_id',
        'operation_id',
        'is_paid',
        'amount',
        'payment_status',
        'transaction_id',

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
