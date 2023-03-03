<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use App\Models\User;
use DateTimeInterface;



class Wallet extends Model
{
    use HasFactory, SoftDeletes,Sluggable;
    protected $table = 'wallet';

    protected $fillable = [  	
        'user_id',
        'earned_amount',
        'amount_spent',
        'balance_amount'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'user_id'
            ]
        ];
    }

    public function getdriver()
    {
        return $this->hasOne(User::class, 'id', 'user_id'); 
    }


}