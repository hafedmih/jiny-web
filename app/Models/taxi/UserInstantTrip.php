<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class UserInstantTrip extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'user_instant_trip';

    protected $fillable = [
        'request_id',
        'firstname',
        'email',
        'lastname',
        'phone_number'
    ];

    protected $appends = ['Image'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getImageAttribute()
    {
        $value = $this->value;
        if (empty($value)) {
            return null;
        }
        
        if ($this->type == "TEXT") {
            return null;
        }

        return getImage('images/settings',$value);
    }
}
