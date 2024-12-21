<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;
use App\Models\taxi\Driver;
use App\Models\boilerplate\Country;




class IndividualPromoMarketing extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'individual_promo_marketing';

    protected $fillable = [
        'promo_name',
        'target_amount',
        'promo_percentage',
        'promo_amount',
        'promo_amount_type',
        'user_id',
        'trip_type',
        'status',
        'marker_id',
        'no_of_times_use',
        'slug'
    ];

  

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'promo_name'
            ]
        ];
    }

   

   
}
