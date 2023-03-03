<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_payment';

    protected $fillable = [
        'user_id',
        'order_id',
        'key_id',
        'currency',
        'receipt',
        'status'
    ];
}
