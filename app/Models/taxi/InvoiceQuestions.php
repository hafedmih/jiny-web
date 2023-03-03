<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;


class InvoiceQuestions extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'invoice_questions';

    protected $fillable = [  	
        'questions',
        'status',
        'slug'
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'questions'
            ]
        ];
    }

    public function solicitante(){
        return $this->belongsTo(RequestQuestions::class, 'question_id', 'id');
    }
}
