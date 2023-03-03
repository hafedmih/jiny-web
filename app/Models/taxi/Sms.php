<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Sms extends Model
{
    use HasFactory, SoftDeletes,Sluggable;
    protected $table = 'sms';

    protected $fillable = [  	
        'title',
        'driver_id',
        'user_id',
        'message',
        // 'has_redirect_url',
        // 'redirect_url',
        'slug',
       
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];
}
