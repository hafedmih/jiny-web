<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class CancellationReason extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = "cancellation_reasons";

    protected $fillable = [
        'reason', 'user_type', 'trip_status', 'pay_status', 'active', 'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'reason'
            ]
        ];
    }
}
