<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Referral extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'referral';

    protected $fillable = [
        'user_id',
        'referred_by'
    ];
    
   
}
