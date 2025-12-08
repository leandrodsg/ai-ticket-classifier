<?php

namespace Tests\Unit;

use App\Models\AiLog;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_has_fillable_attributes(): void
    {
        // Test only fillable attributes (guarded ones are set by AI classification)
        $ticket = new Ticket([
            'title' => 'Test Title',
            'description' => 'Test Description',
            'status' => 'open',
        ]);

        $this->assertEquals('Test Title', $ticket->title);
        $this->assertEquals('Test Description', $ticket->description);
        $this->assertEquals('open', $ticket->status);

        // Guarded attributes should not be set via constructor
        $this->assertNull($ticket->category);
        $this->assertNull($ticket->sentiment);
        $this->assertNull($ticket->confidence);
        $this->assertNull($ticket->priority);
    }

    public function test_sla_due_at_is_cast_to_datetime(): void
    {
        $ticket = Ticket::factory()->create([
            'sla_due_at' => '2025-12-10 15:00:00'
        ]);

        $this->assertInstanceOf(Carbon::class, $ticket->sla_due_at);
        $this->assertEquals('2025-12-10 15:00:00', $ticket->sla_due_at->format('Y-m-d H:i:s'));
    }

    public function test_escalated_at_is_cast_to_datetime(): void
    {
        $ticket = Ticket::factory()->create([
            'escalated_at' => '2025-12-08 10:00:00'
        ]);

        $this->assertInstanceOf(Carbon::class, $ticket->escalated_at);
    }

    public function test_ai_classification_log_is_cast_to_array(): void
    {
        $log = ['model' => 'gpt-4', 'confidence' => 0.9];
        
        $ticket = Ticket::factory()->create([
            'ai_classification_log' => $log
        ]);

        $this->assertIsArray($ticket->ai_classification_log);
        $this->assertEquals('gpt-4', $ticket->ai_classification_log['model']);
        $this->assertEquals(0.9, $ticket->ai_classification_log['confidence']);
    }

    public function test_ticket_has_ai_logs_relationship(): void
    {
        $ticket = Ticket::factory()->create();
        
        AiLog::factory()->count(3)->create([
            'ticket_id' => $ticket->id
        ]);

        $this->assertCount(3, $ticket->aiLogs);
        $this->assertInstanceOf(AiLog::class, $ticket->aiLogs->first());
    }

    public function test_latest_ai_log_returns_most_recent(): void
    {
        $ticket = Ticket::factory()->create();
        
        $oldLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'created_at' => Carbon::now()->subHours(2)
        ]);
        
        $newLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'created_at' => Carbon::now()
        ]);

        $latest = $ticket->latestAiLog();
        
        $this->assertEquals($newLog->id, $latest->id);
    }

    public function test_latest_ai_log_returns_null_when_no_logs(): void
    {
        $ticket = Ticket::factory()->create();

        $latest = $ticket->latestAiLog();
        
        $this->assertNull($latest);
    }

    public function test_soft_deletes_trait_is_used(): void
    {
        $ticket = Ticket::factory()->create();
        $ticketId = $ticket->id;

        $ticket->delete();

        // Should be soft deleted
        $this->assertSoftDeleted('tickets', ['id' => $ticketId]);
        
        // Should not be found in normal queries
        $this->assertNull(Ticket::find($ticketId));
        
        // Should be found in withTrashed queries
        $this->assertNotNull(Ticket::withTrashed()->find($ticketId));
    }

    public function test_ticket_can_be_restored_after_soft_delete(): void
    {
        $ticket = Ticket::factory()->create();
        $ticketId = $ticket->id;

        $ticket->delete();
        $this->assertSoftDeleted('tickets', ['id' => $ticketId]);

        $ticket->restore();
        $this->assertNotSoftDeleted('tickets', ['id' => $ticketId]);
    }

    public function test_ticket_factory_creates_valid_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertNotNull($ticket->id);
        $this->assertNotNull($ticket->title);
        $this->assertNotNull($ticket->description);
        $this->assertNotNull($ticket->status);
    }

    public function test_ticket_timestamps_are_set(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertNotNull($ticket->created_at);
        $this->assertNotNull($ticket->updated_at);
        $this->assertInstanceOf(Carbon::class, $ticket->created_at);
        $this->assertInstanceOf(Carbon::class, $ticket->updated_at);
    }
}
