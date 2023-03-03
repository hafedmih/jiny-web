<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class DocumentsGroup extends Model
{
    use HasFactory,SoftDeletes,Sluggable;

    protected $table = "document_group";

    protected $fillable = ['slug','name','status'];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function driverDocument() {
        return $this->hasMany(DriverDocument::class, 'group_by' ,'id');
    }

    public function getDocument() {
        return $this->hasMany(Documents::class, 'group_by' ,'id');
    }
    

}