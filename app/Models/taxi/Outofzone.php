<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;


use App\Models\boilerplate\Country;

class Outofzone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'out_of_zone';

    protected $fillable = [
         'id','kilometer','price','status'
    ];

    public function getCountry()
    {
        return $this->hasOne(Country::class, 'id', 'country_code');
    }

    protected $hidden = ['created_at','updated_at','deleted_at'];

  
}