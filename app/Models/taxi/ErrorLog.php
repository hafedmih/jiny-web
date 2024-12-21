<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErrorLog extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "error_log";

    protected $fillable = ['error','created_at','updated_at'];

  
}
