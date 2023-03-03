<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;


class Email extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    protected $table = 'email';

    protected $fillable = [  	
        'driver_id',
        'user_id',
        'subject',
        'content',
        'slug',
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'subject'
            ]
        ];
    }
}
