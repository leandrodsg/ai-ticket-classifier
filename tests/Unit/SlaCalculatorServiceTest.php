<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Services\SlaCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlaCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlaCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SlaCalculatorService();
    }

    public function test_calculate_due_date_for_critical_priority(): void
    {
        $createdAt = Carbon::parse('2025-12-08 10:00:00');
        $dueDate = $this->service->calculateDueDate('critical', $createdAt);

        // Critical should have 1 hour SLA
        $expected = $createdAt->copy()->addHours(1);
        $this->assertEquals($expected, $dueDate);
    }

    public function test_calculate_due_date_for_high_priority(): void
    {
        $createdAt = Carbon::parse('2025-12-08 10:00:00');
        $dueDate = $this->service->calculateDueDate('high', $createdAt);

        // High should have 4 hours SLA
        $expected = $createdAt->copy()->addHours(4);
        $this->assertEquals($expected, $dueDate);
    }

    public function test_calculate_due_date_for_medium_priority(): void
    {
        $createdAt = Carbon::parse('2025-12-08 10:00:00');
        $dueDate = $this->service->calculateDueDate('medium', $createdAt);

        // Medium should have 24 hours SLA
        $expected = $createdAt->copy()->addHours(24);
        $this->assertEquals($expected, $dueDate);
    }

    public function test_calculate_due_date_for_low_priority(): void
    {
        $createdAt = Carbon::parse('2025-12-08 10:00:00');
        $dueDate = $this->service->calculateDueDate('low', $createdAt);

        // Low should have 48 hours SLA
        $expected = $createdAt->copy()->addHours(48);
        $this->assertEquals($expected, $dueDate);
    }

    public function test_calculate_due_date_throws_exception_for_invalid_priority(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $createdAt = Carbon::now();
        $this->service->calculateDueDate('invalid_priority', $createdAt);
    }

    public function test_is_sla_breached_returns_true_when_past_due(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => Carbon::now()->subHour(),
            'status' => 'open'
        ]);

        $result = $this->service->isSlaBreached($ticket);

        $this->assertTrue($result);
    }

    public function test_is_sla_breached_returns_false_when_not_due_yet(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => Carbon::now()->addHour(),
            'status' => 'open'
        ]);

        $result = $this->service->isSlaBreached($ticket);

        $this->assertFalse($result);
    }

    public function test_is_sla_breached_returns_false_for_closed_ticket(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => Carbon::now()->subHour(),
            'status' => 'closed'
        ]);

        $result = $this->service->isSlaBreached($ticket);

        $this->assertFalse($result);
    }

    public function test_is_sla_breached_returns_false_when_no_sla_set(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => null,
            'status' => 'open'
        ]);

        $result = $this->service->isSlaBreached($ticket);

        $this->assertFalse($result);
    }

    public function test_is_sla_breached_works_with_array(): void
    {
        $ticketArray = [
            'id' => 1,
            'sla_due_at' => Carbon::now()->subHour()->toDateTimeString(),
            'status' => 'open'
        ];

        $result = $this->service->isSlaBreached($ticketArray);

        $this->assertTrue($result);
    }

    public function test_get_sla_status_returns_no_sla_when_not_set(): void
    {
        $ticket = Ticket::factory()->make(['sla_due_at' => null]);

        $status = $this->service->getSlaStatus($ticket);

        $this->assertEquals('no_sla', $status['status']);
        $this->assertFalse($status['breached']);
        $this->assertNull($status['remaining_hours']);
        $this->assertNull($status['remaining_percentage']);
    }

    public function test_get_sla_status_returns_breached_when_past_due(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => Carbon::now()->subHours(2),
            'status' => 'open'
        ]);

        $status = $this->service->getSlaStatus($ticket);

        $this->assertEquals('breached', $status['status']);
        $this->assertTrue($status['breached']);
    }

    public function test_get_sla_status_returns_on_track_when_plenty_time(): void
    {
        // Create ticket with 48h SLA, 2 hours passed, 46 hours remaining (> 50%)
        $createdAt = Carbon::now()->subHours(2);
        $ticket = Ticket::factory()->create([
            'sla_due_at' => $createdAt->copy()->addHours(48),
            'status' => 'open',
        ]);
        
        // Manually update created_at after creation
        $ticket->created_at = $createdAt;
        $ticket->save();
        
        // Refresh to get actual database values
        $ticket->refresh();

        $status = $this->service->getSlaStatus($ticket);

        $this->assertEquals('on_track', $status['status']);
        $this->assertFalse($status['breached']);
        $this->assertGreaterThan(0, $status['remaining_hours']);
        $this->assertGreaterThan(50, $status['remaining_percentage']);
    }

    public function test_get_sla_status_returns_at_risk_when_little_time(): void
    {
        // Create ticket with 24h SLA, 20 hours passed, 4 hours remaining (< 25%)
        $createdAt = Carbon::now()->subHours(20);
        $slaDueAt = $createdAt->copy()->addHours(24);
        
        $ticket = Ticket::factory()->make([
            'sla_due_at' => $slaDueAt,
            'status' => 'open',
            'created_at' => $createdAt,
        ]);

        $status = $this->service->getSlaStatus($ticket);

        // Should be 'critical' or 'warning' based on remaining percentage
        $this->assertContains($status['status'], ['critical', 'warning']);
        $this->assertFalse($status['breached']);
        $this->assertLessThan(50, $status['remaining_percentage']);
    }

    public function test_get_sla_status_calculates_remaining_hours_correctly(): void
    {
        $ticket = Ticket::factory()->make([
            'sla_due_at' => Carbon::now()->addHours(5),
            'status' => 'open'
        ]);

        $status = $this->service->getSlaStatus($ticket);

        $this->assertEqualsWithDelta(5, $status['remaining_hours'], 0.1);
    }

    public function test_get_sla_status_works_with_array_input(): void
    {
        $ticketArray = [
            'id' => 1,
            'sla_due_at' => Carbon::now()->addHours(10)->toDateTimeString(),
            'status' => 'open',
            'created_at' => Carbon::now()->subHours(2)->toDateTimeString(),
        ];

        $status = $this->service->getSlaStatus($ticketArray);

        $this->assertIsArray($status);
        $this->assertArrayHasKey('status', $status);
        $this->assertArrayHasKey('breached', $status);
    }

    public function test_validate_priority_throws_exception_for_empty_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->service->calculateDueDate('', Carbon::now());
    }

    public function test_get_sla_hours_returns_correct_hours(): void
    {
        $hours = $this->service->getSlaHours('critical');
        $this->assertEquals(1, $hours);

        $hours = $this->service->getSlaHours('high');
        $this->assertEquals(4, $hours);

        $hours = $this->service->getSlaHours('medium');
        $this->assertEquals(24, $hours);

        $hours = $this->service->getSlaHours('low');
        $this->assertEquals(48, $hours);
    }
}
