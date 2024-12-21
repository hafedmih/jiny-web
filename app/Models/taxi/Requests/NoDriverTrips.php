<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class NoDriverTrips extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'no_driver_trip';

    protected $fillable = [
        'user_id','pick_up','drop','datetime','trip_type'
    ];

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
