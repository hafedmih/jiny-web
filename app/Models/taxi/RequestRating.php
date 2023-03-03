<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;
use App\Models\taxi\Requests\Request;


class RequestRating extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'request_rating';

    protected $fillable = [  	
        'user_id',
        'request_id',
        'rating',   
        'feedback'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function request() {
        return $this->hasOne(Request::class, 'id', 'request_id');
    }


}
