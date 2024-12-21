<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ReferalAmountList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'referal_amount_list';

    protected $fillable = [  	
        'user_id',
        'referal_user_id',
        'amount',   
        'status'
    ];

    // protected $hidden = ['created_at', 'updated_at'];

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getReferalUser() {
        return $this->hasOne(User::class, 'id', 'referal_user_id');
    }
}
