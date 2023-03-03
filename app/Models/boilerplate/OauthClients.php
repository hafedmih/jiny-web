<?php

namespace App\Models\boilerplate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\Uuids;

class OauthClients extends Model
{
    use HasFactory;
    use Uuids;

    protected $table = "oauth_clients";

    protected $fillable = ['id','user_id','name','secret','provider','redirect','personal_access_client','password_client','revoked'];


   
}
