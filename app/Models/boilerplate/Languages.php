<?php

namespace App\Models\boilerplate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Languages extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'created_at',
    ];

    protected $appends = ['updated_date'] ;
    public function getUpdatedDateAttribute(){
        $date = Carbon::createFromDate($this->updated_at); 
        $dateInMills = $date->timestamp;
        return $dateInMills;
    }
}
