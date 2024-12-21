<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Documents extends Model
{
    use HasFactory,SoftDeletes,Sluggable;

    protected $table = "documents";

    protected $fillable = ['slug','document_name','requried','expiry_date','status','identifier','group_by'];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'document_name'
            ]
        ];
    }

    public function drivdocument() {
        return $this->hasOne(DriverDocument::class, 'document_id' ,'id');
    }

    public function getDocumentGroup() {
        return $this->hasOne(DocumentsGroup::class, 'id', 'group_by');
    }
}
