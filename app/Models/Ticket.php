<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'sentiment',
        'status',
        'ai_classification_log',
    ];

    protected $casts = [
        'ai_classification_log' => 'array',
    ];
}
