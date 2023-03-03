<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'category';

    protected $fillable = [
        'category_name',
        'category_image',
        'status',
        'slug'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'category_name'
            ]
        ];
    }

    public function getCategoryImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return getImage('images/category',$value);
    }
}
