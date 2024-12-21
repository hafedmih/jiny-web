<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class DumpCompany extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'dummy_company';

    protected $fillable = [
        'company_name',
        'company_phone_number',
        'status',
        'slug',
        'total_no_of_vehicle',
        'user_id'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'company_name'
            ]
        ];
    }
}
