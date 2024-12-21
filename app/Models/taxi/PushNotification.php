<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class PushNotification extends Model
{
    use HasFactory,Sluggable;
    protected $table = 'pushnotif';

    protected $fillable = [  	
        'title',
        'suptitle',
        'status',
        'slug',
        'key',
        'language'
               
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'key'
            ]
        ];
    }

    // protected $hidden = [
    //     'deleted_at',
    //     'updated_at',
    // ];
}
