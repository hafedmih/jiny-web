<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Sos extends Model
{
    use HasFactory, SoftDeletes,Sluggable;
    protected $table = 'sos';

    protected $fillable = [  	
        'phone_number',
        'title',
        'status',
        'slug',
        'created_by'
       
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
