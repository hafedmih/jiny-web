<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'customer_details';

    protected $fillable = [
        'request_id',
        'customer_name',
        'customer_number',
        'customer_address',
        'customer_slug',
        'status',
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'customer_name'
            ]
        ];
    }
}
