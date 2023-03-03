<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;
use App\Models\taxi\Driver;



class Target extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'target_management';

    protected $fillable = [
        'target_name',
        'target_icon',
        'driver_id',
        'service_location',
        'target_driver_count',
        'target_driver_from_date',
        'target_select_package',
        'target_driver_to_date',
        'target_driver_type',
        'status',
        'amount',
        'no_of_trips',
        'target_duration',
        'slug'
    ];

    public function gettargetIconAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return getImage('images/target',$value);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'driver_id', 'id');
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'target_name'
            ]
        ];
    }
}
