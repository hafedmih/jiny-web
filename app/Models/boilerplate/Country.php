<?php

namespace App\Models\boilerplate;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Country extends Authenticatable
{
    use HasFactory, Notifiable;
    use UuidModelTrait;

    protected $table = 'country';

    protected $id;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'dial_code',
        'code',
        'currency_name',
        'currency_code',
        'currency_symbol',
        'status',
        'capital',
        'citizenship',
        'country_code',
        'currency',
        'currency_sub_unit',
        'full_name',
        'iso_3166_3',
        'region_code',
        'sub_region_code',
        'eea',
        'currency_decimals',
        'flag',
        'flag_base_64',
        'time_zone',
        'gmt_offset'     
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function country()
    {
        return $this->morphTo();
    }


    // public function address()
    // {
    //     return $this->belongsTo(Country::class,'country', 'id');
    // }

    // public function serviceLocation()
    // {
    //     return $this->hasMany(ServiceLocation::class,'country', 'id');
    // }

   
}
