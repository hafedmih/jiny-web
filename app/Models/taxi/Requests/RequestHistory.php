<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestHistory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'request_history';
    
    protected $fillable = ['request_id','olat','olng','dlat','dlng','pick_address','drop_address'];
}
