<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSetAmount extends Model
{
    use HasFactory;

    protected $table = 'request_set_amount';

    protected $fillable = ['request_id','request_amount','amount_per_km','status'];

    public function request()
    {
        return $this->belongsTo(Request::class,'request_id','id');
    }

}
