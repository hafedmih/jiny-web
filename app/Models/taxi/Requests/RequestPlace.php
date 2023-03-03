<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPlace extends Model
{
    use HasFactory;

    protected $table = 'request_places';
    
    protected $fillable = ['request_id','pick_lat','pick_lng','drop_lat','drop_lng','pick_address','drop_address','active','request_path','poly_string','stops','stop_lat','stop_lng','stop_address','pick_up_id','drop_id','stop_id'];
}
