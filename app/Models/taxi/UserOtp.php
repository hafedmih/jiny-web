<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserOtp extends Model
{
    use HasFactory;

    protected $table = "user_otp";

    protected $fillable = [
        'phone_number', 'country_code', 'otp'
    ];


}
