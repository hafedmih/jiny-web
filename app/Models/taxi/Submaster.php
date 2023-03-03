<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Submaster extends Model
{
    use HasFactory,Sluggable;
    protected $table = 'submaster';

    protected $fillable = [  	
        'id',
        'name',
        'amount',
        'validity',
        'description'       
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    // protected $hidden = [
    //     'deleted_at',
    //     'updated_at',
    // ];
}
