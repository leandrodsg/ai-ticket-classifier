<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SlaCalculatorService
{
    /**
     * Calculate SLA due date based on priority.
     *
     * @param string $priority
     * @param Carbon $createdAt
     * @return Carbon
     * @throws \InvalidArgumentException
     */
    public function calculateDueDate(string $priority, Carbon $createdAt): Carbon
    {
        $this->validatePriority($priority);

        $slaHours = config('priority.slas')[$priority];

        $dueDate = $createdAt->copy()->addHours($slaHours);

        Log::info('Calculated SLA due date', [
            'priority' => $priority,
            'sla_hours' => $slaHours,
            'created_at' => $createdAt->toISOString(),
            'due_date' => $dueDate->toISOString(),
        ]);

        return $dueDate;
    }

    /**
     * Check if a ticket has breached its SLA.
     *
     * @param mixed $ticket
     * @return bool
     */
    public function isSlaBreached($ticket): bool
    {
        // Handle both model instances and arrays
        $slaDueAt = is_object($ticket) ? $ticket->sla_due_at : ($ticket['sla_due_at'] ?? null);
        $status = is_object($ticket) ? $ticket->status : ($ticket['status'] ?? null);

        // No SLA set
        if (!$slaDueAt) {
            return false;
        }

        // Convert to Carbon if needed
        if (!$slaDueAt instanceof Carbon) {
            $slaDueAt = Carbon::parse($slaDueAt);
        }

        // Closed tickets don't breach SLA
        if ($status === 'closed') {
            return false;
        }

        $isBreached = $slaDueAt->isPast();

        if ($isBreached) {
            Log::warning('SLA breached', [
                'ticket_id' => is_object($ticket) ? $ticket->id : ($ticket['id'] ?? 'unknown'),
                'sla_due_at' => $slaDueAt->toISOString(),
                'current_time' => now()->toISOString(),
            ]);
        }

        return $isBreached;
    }

    /**
     * Get SLA status for a ticket.
     *
     * @param mixed $ticket
     * @return array
     */
    public function getSlaStatus($ticket): array
    {
        $slaDueAt = is_object($ticket) ? $ticket->sla_due_at : ($ticket['sla_due_at'] ?? null);
        $status = is_object($ticket) ? $ticket->status : ($ticket['status'] ?? null);

        if (!$slaDueAt) {
            return [
                'status' => 'no_sla',
                'breached' => false,
                'remaining_hours' => null,
                'remaining_percentage' => null,
            ];
        }

        // Convert to Carbon if needed
        if (!$slaDueAt instanceof Carbon) {
            $slaDueAt = Carbon::parse($slaDueAt);
        }

        $now = now();
        $isBreached = $this->isSlaBreached($ticket);

        if ($isBreached) {
            return [
                'status' => 'breached',
                'breached' => true,
                'remaining_hours' => 0,
                'remaining_percentage' => 0,
            ];
        }

        // Closed tickets are considered on time
        if ($status === 'closed') {
            return [
                'status' => 'on_time',
                'breached' => false,
                'remaining_hours' => null,
                'remaining_percentage' => null,
            ];
        }

        // Get created_at (handle both object and array)
        $createdAt = is_object($ticket) ? $ticket->created_at : ($ticket['created_at'] ?? null);
        if (!$createdAt) {
            $createdAt = now()->subHours(1);
        } elseif (!$createdAt instanceof Carbon) {
            $createdAt = Carbon::parse($createdAt);
        }

        $totalDuration = $createdAt->diffInHours($slaDueAt);
        $remainingHours = $now->diffInHours($slaDueAt, false); // false = positive for future dates
        $remainingPercentage = $totalDuration > 0 ? ($remainingHours / $totalDuration) * 100 : 0;

        // Determine status based on remaining time
        $statusLabel = match(true) {
            $remainingPercentage > 50 => 'on_track',
            $remainingPercentage > 25 => 'warning',
            default => 'critical',
        };

        return [
            'status' => $statusLabel,
            'breached' => false,
            'remaining_hours' => max(0, $remainingHours),
            'remaining_percentage' => max(0, $remainingPercentage),
        ];
    }

    /**
     * Calculate SLA due date from priority data.
     *
     * @param array $priorityData
     * @param Carbon $createdAt
     * @return Carbon
     */
    public function calculateFromPriorityData(array $priorityData, Carbon $createdAt): Carbon
    {
        $priority = $priorityData['priority'] ?? 'low';
        return $this->calculateDueDate($priority, $createdAt);
    }

    /**
     * Validate that priority level is valid.
     *
     * @param string $priority
     * @throws \InvalidArgumentException
     */
    protected function validatePriority(string $priority): void
    {
        $validPriorities = config('priority.valid_priorities');

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid priority level: {$priority}. Valid: " . implode(', ', $validPriorities));
        }
    }

    /**
     * Get SLA hours for a priority level.
     *
     * @param string $priority
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getSlaHours(string $priority): int
    {
        $this->validatePriority($priority);
        return config('priority.slas')[$priority];
    }
}
