<?php

namespace App\Models\taxi\Requests;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestMeta extends Model
{
    use HasFactory;

    protected $table = 'request_meta';

    protected $fillable = ['request_id','user_id','driver_id','active','is_later','assign_method'];

    public function request()
    {
        return $this->belongsTo(Request::class,'request_id','id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}
