<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class RiderAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "rider_address";

    protected $fillable = [
        'latitude','longitude','title','address','riderId'
    ];

    
}
