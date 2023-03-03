<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDriverLog extends Model
{
    use HasFactory;

    protected $table = 'request_driver_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['request_id','user_id','driver_lat','driver_lng','date_time','type','user_type','status'];

    public function requestDetail()
    {
        return $this->belongsTo(Request::class, 'request_id', 'id');
    }
}
