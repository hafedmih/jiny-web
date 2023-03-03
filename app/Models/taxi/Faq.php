<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;


class Faq extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    protected $table = 'faq';

    protected $fillable = [  	
        'question',
        'answer',
        'category',
        'status',
        'language',
        'slug'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'question'
            ]
        ];
    }
}
