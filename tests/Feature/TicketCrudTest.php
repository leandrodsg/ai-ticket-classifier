<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_index_page_loads(): void
    {
        $response = $this->get('/tickets');

        $response->assertStatus(200);
        $response->assertViewIs('tickets.index');
    }

    public function test_ticket_create_page_loads(): void
    {
        $response = $this->get('/tickets/create');

        $response->assertStatus(200);
        $response->assertViewIs('tickets.create');
    }

    public function test_can_create_ticket_with_valid_data(): void
    {
        $ticketData = [
            'title' => 'Test Ticket Title',
            'description' => 'This is a valid description with more than 10 characters for testing purposes.',
            'status' => 'open',
        ];

        $response = $this->withoutMiddleware()->post('/tickets', $ticketData);

        $response->assertRedirect(); // Should redirect to show page

        // Check that ticket was created with basic data
        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket Title',
            'description' => 'This is a valid description with more than 10 characters for testing purposes.',
            'status' => 'open',
        ]);

        // Check that ticket exists
        $ticket = \App\Models\Ticket::where('title', 'Test Ticket Title')->first();
        $this->assertNotNull($ticket);

        // Note: AI classification may not work in test environment due to API limitations
        // The important thing is that the ticket was created successfully
        // AI classification is tested separately in AiClassificationTest
    }

    public function test_can_view_ticket_details(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
        $response->assertViewIs('tickets.show');
        $response->assertViewHas('ticket', $ticket);
    }

    public function test_can_update_ticket(): void
    {
        // Create ticket with basic data first
        $ticket = Ticket::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original description that will be changed.',
            'status' => 'open',
        ]);

        // Ensure ticket has valid ID
        $this->assertNotNull($ticket->id);
        $this->assertIsInt($ticket->id);

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated description with sufficient length.',
            'status' => 'closed',
        ];

        $response = $this->put("/tickets/{$ticket->id}", $updatedData);

        $response->assertRedirect(); // Should redirect to show page

        // Check basic fields were updated
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
            'description' => 'Updated description with sufficient length.',
            'status' => 'closed',
        ]);

        // Check that priority was recalculated (since description changed)
        $updatedTicket = Ticket::find($ticket->id);
        $this->assertNotNull($updatedTicket->priority);
        $this->assertNotNull($updatedTicket->sla_due_at);
    }
}
