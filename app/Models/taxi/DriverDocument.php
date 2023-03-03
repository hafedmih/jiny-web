<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverDocument extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "driver_document";

    protected $fillable = ['user_id','document_id','document_image','expiry_date','issue_date','document_status','status','exprienc_status','exprience_reson','identifier','group_by'];

   protected $appends = ['document_images'];
   
    public function getDocumentImagesAttribute($value)
    {
        $value = $this->document_image;
        if (empty($value)) {
            return null;
        }
        return getImage('images/document',$value);
    }

    public function document() {
        return $this->hasOne(Documents::class, 'id', 'document_id');
    }

    // public function getDriverDocumentGroup() {
    //     return $this->hasMany(DocumentsGroup::class, 'id', 'group_by');
    // }
}
