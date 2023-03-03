<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;
use App\Models\taxi\Driver;
use App\Models\boilerplate\Country;




class Promocode extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'promocode';

    protected $fillable = [
        'zone_id',
        'promo_code',
        'description',
        'user_id',
        'distance_km',
        'from_date',
        'to_date',
        'select_offer_option',
        'promo_offer_no_of_ride',
        'target_amount',
        'promo_type',
        'promo_icon',
        'description',
        'promo_type',
        'amount',
        'percentage',
        'promo_use_count',
        'trip_type',
        'promo_user_reuse_count',
        'new_user_count'
    ];

    public function getPromoIconAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
      return  getImage('promo',$value);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'promo_code'
            ]
        ];
    }

    public function getTypesAttribute()
    {
        $value = $this->types_id;
        if (empty($value)) {
            return null;
        }
        return explode(',',$value);
    }

    public function zone() {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }

    public function GetCountry()
    {
        return $this->belongsTo(Country::class,'country', 'id');
    }

   
}
