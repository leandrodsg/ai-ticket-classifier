<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Get the AI logs for this ticket.
     */
    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class);
    }

    /**
     * Get the latest AI classification for this ticket.
     */
    public function latestAiLog()
    {
        return $this->aiLogs()->latest()->first();
    }

    /**
     * Check if the ticket was classified by AI.
     */
    public function isAiClassified(): bool
    {
        return !empty($this->category) && !empty($this->sentiment);
    }
}
