<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Tripcomplaint extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'trip_complaints';

    protected $fillable = [
        'title',
        // 'category',
        'type',
        'status',
        'slug',
        'complaint_type',
        'language',
        
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
