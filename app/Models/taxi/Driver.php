<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\User;
use App\Models\DumpCompany;


class Driver extends Model
{
    use HasFactory,SoftDeletes,Sluggable;

    protected $table = "drivers";

    protected $fillable = ['slug','type','car_number','car_model','car_year','car_colour','user_id','status','service_location', 'is_available', 'is_active', 'is_approve', 'total_trip', 'total_accept', 'total_reject', 'acceptance_ratio','reject_count','document_upload_status','city','state','pincode','subscription_type','login_method','company_id','service_category','brand_label','login_method','notes','approved_by','overall_rating'];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'car_number'
            ]
        ];
    }
    public function zone() {
        return $this->hasOne(Zone::class, 'id', 'service_location');
    }

    public function vehicletype() {
        return $this->hasOne(Vehicle::class, 'id', 'type');
    }

    public function getApprover() {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    public function companys() {
        return $this->hasOne(User::class, 'id', 'company_id');
    }


}
