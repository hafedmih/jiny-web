<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\taxi\Requests\Request;
// use Cviebrock\EloquentSluggable\Sluggable;

class UserComplaint extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "user_complaints";

    protected $fillable = ['answer','user_id','complaint_id','status','category','request_id','tripcomplaint_id'];

    public function userDetails()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function complaintDetails()
    {
        return $this->hasOne(Complaint::class, 'id', 'complaint_id');
    }
    
    public function requestDetails()
    {
        return $this->hasOne(Request::class, 'id', 'request_id');
    }
    public function tripComplaints()
    {
        return $this->hasOne(Tripcomplaint::class, 'id','tripcomplaint_id');
    }

    
}
