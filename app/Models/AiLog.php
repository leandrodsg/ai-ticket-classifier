<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'model',
        'prompt',
        'response',
        'confidence',
        'processing_time_ms',
        'status',
        'error_message',
    ];

    protected $casts = [
        'response' => 'array',
        'confidence' => 'decimal:2',
        'processing_time_ms' => 'integer',
    ];

    /**
     * Get the ticket that this AI log belongs to.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Check if the AI classification was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Get the confidence as a percentage.
     */
    public function getConfidencePercentageAttribute(): ?float
    {
        return $this->confidence ? round($this->confidence * 100, 1) : null;
    }
}
