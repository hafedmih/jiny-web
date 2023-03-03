<?php

namespace App\Models\boilerplate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CompanyDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_details';

    protected $fillable = [
        'user_id','no_of_vehicle','status','company_name','company_code','alternative_number','commission','created_by',
    ];
}

