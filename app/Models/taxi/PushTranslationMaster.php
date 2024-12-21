<?php

namespace App\Models\taxi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushTranslationMaster extends Model
{
    use HasFactory;

    protected $table = 'push_notification_translation_master';

    protected $fillable = [
        'title',
        'description',
        'language',
        'key_value',
    ];
    
}
