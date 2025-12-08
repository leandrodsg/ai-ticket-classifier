<?php

namespace Tests\Unit;

use App\Models\AiLog;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiLogModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_log_has_fillable_attributes(): void
    {
        $aiLog = new AiLog([
            'ticket_id' => 1,
            'model' => 'gpt-4',
            'prompt' => 'Test prompt',
            'response' => ['category' => 'technical'],
            'confidence' => 0.95,
            'processing_time_ms' => 150,
            'status' => 'success',
            'error_message' => null,
        ]);

        $this->assertEquals(1, $aiLog->ticket_id);
        $this->assertEquals('gpt-4', $aiLog->model);
        $this->assertEquals('Test prompt', $aiLog->prompt);
        $this->assertEquals(0.95, $aiLog->confidence);
        $this->assertEquals(150, $aiLog->processing_time_ms);
        $this->assertEquals('success', $aiLog->status);
    }

    public function test_response_is_cast_to_array(): void
    {
        $ticket = Ticket::factory()->create();
        
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'response' => ['category' => 'technical', 'sentiment' => 'positive']
        ]);

        $this->assertIsArray($aiLog->response);
        $this->assertEquals('technical', $aiLog->response['category']);
        $this->assertEquals('positive', $aiLog->response['sentiment']);
    }

    public function test_confidence_is_cast_to_decimal(): void
    {
        $ticket = Ticket::factory()->create();
        
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'confidence' => 0.85
        ]);

        $this->assertIsString($aiLog->confidence);
        $this->assertEquals('0.850', $aiLog->confidence);
    }

    public function test_processing_time_ms_is_cast_to_integer(): void
    {
        $ticket = Ticket::factory()->create();
        
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'processing_time_ms' => '250'
        ]);

        $this->assertIsInt($aiLog->processing_time_ms);
        $this->assertEquals(250, $aiLog->processing_time_ms);
    }

    public function test_ai_log_belongs_to_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create(['ticket_id' => $ticket->id]);

        $this->assertInstanceOf(Ticket::class, $aiLog->ticket);
        $this->assertEquals($ticket->id, $aiLog->ticket->id);
    }

    public function test_is_successful_returns_true_for_success_status(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'status' => 'success'
        ]);

        $this->assertTrue($aiLog->isSuccessful());
    }

    public function test_is_successful_returns_false_for_error_status(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'status' => 'error'
        ]);

        $this->assertFalse($aiLog->isSuccessful());
    }

    public function test_get_confidence_percentage_attribute_returns_correct_value(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'confidence' => 0.857
        ]);

        $this->assertEquals(85.7, $aiLog->confidence_percentage);
    }

    public function test_get_confidence_percentage_attribute_returns_null_when_no_confidence(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'confidence' => null
        ]);

        $this->assertNull($aiLog->confidence_percentage);
    }

    public function test_ai_log_factory_creates_valid_log(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create(['ticket_id' => $ticket->id]);

        $this->assertNotNull($aiLog->id);
        $this->assertNotNull($aiLog->ticket_id);
        $this->assertNotNull($aiLog->model);
        $this->assertNotNull($aiLog->status);
    }

    public function test_ai_log_can_store_error_message(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create([
            'ticket_id' => $ticket->id,
            'status' => 'error',
            'error_message' => 'API timeout'
        ]);

        $this->assertEquals('error', $aiLog->status);
        $this->assertEquals('API timeout', $aiLog->error_message);
        $this->assertFalse($aiLog->isSuccessful());
    }

    public function test_timestamps_are_set_on_creation(): void
    {
        $ticket = Ticket::factory()->create();
        $aiLog = AiLog::factory()->create(['ticket_id' => $ticket->id]);

        $this->assertNotNull($aiLog->created_at);
        $this->assertNotNull($aiLog->updated_at);
    }
}
