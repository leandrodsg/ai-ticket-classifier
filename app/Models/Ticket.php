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
        'status',
    ];

    protected $guarded = [
        'category',
        'sentiment',
        'confidence',
        'priority',
        'sla_due_at',
        'impact_level',
        'urgency_level',
        'escalated_at',
        'ai_classification_log',
    ];

    protected $casts = [
        'ai_classification_log' => 'array',
        'sla_due_at' => 'datetime',
        'escalated_at' => 'datetime',
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

    /**
     * Scope for critical priority tickets.
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Scope for tickets with overdue SLA.
     */
    public function scopeSlaOverdue($query)
    {
        return $query->whereNotNull('sla_due_at')
                    ->where('sla_due_at', '<', now())
                    ->where('status', '!=', 'closed');
    }

    /**
     * Check if ticket has overdue SLA.
     */
    public function isSlaOverdue(): bool
    {
        return $this->sla_due_at && $this->sla_due_at->isPast() && $this->status !== 'closed';
    }

    /**
     * Check if ticket is escalated.
     */
    public function isEscalated(): bool
    {
        return !is_null($this->escalated_at);
    }
}
