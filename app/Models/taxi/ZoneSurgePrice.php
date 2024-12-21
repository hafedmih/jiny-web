<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoneSurgePrice extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "zone_surge_price";

    protected $fillable = ['zone_id','surge_price','surge_distance_price','start_time','end_time','available_days','status'];
}
