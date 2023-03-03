<?php

namespace App\Models\taxi\Requests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'trip';

}
