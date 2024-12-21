<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use App\Models\taxi\Requests\Request;

 
class RequestQuestions extends Model
{
    use HasFactory;
    
    protected $table = 'request_questions';

    protected $fillable = [  	
        'request_id',
        'question_id',
        'user_id',
        'status',
        'answer'
    ];

    public function questionDetails()
    {
        return $this->hasOne(InvoiceQuestions::class, 'id','question_id');
    }
    public function requestquestions()
    {
        return $this->hasOne(Request::class, 'id','request_id');
    }
    


}
