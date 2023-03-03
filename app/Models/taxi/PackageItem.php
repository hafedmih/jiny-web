<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class PackageItem extends Model
{
    use HasFactory;
    protected $table = 'package_item';

    protected $fillable = [  	
        'id',
        'package_id',
        'type_id',
        'price',
        'slug',
        'status'      
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    // public function sluggable(): array
    // {
    //     return [
    //         'slug' => [
    //             'source' => 'name'
    //         ]
    //     ];
    // }

    public function getVehicle() {
        return $this->hasOne(Vehicle::class, 'id', 'type_id');
    }

    public function getPackage() {
        return $this->hasOne(PackageMaster::class, 'id', 'package_id');
    }

}
