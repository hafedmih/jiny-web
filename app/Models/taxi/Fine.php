<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use App\Models\User;



class Fine extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'fine';

    protected $fillable = [  	
        'user_id',
        'fine_amount',
        'description'
    ];

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function driverDetail()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }
    
  

   
}
