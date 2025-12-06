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

        $response = $this->post('/tickets', $ticketData);

        $response->assertRedirect(); // Should redirect to show page

        // Check that ticket was created with basic data
        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket Title',
            'description' => 'This is a valid description with more than 10 characters for testing purposes.',
            'status' => 'open',
        ]);

        // Check that AI classification was added
        $ticket = \App\Models\Ticket::where('title', 'Test Ticket Title')->first();
        $this->assertNotNull($ticket->category);
        $this->assertNotNull($ticket->sentiment);
        $this->assertContains($ticket->category, ['technical', 'commercial', 'billing', 'general', 'support']);
        $this->assertContains($ticket->sentiment, ['positive', 'negative', 'neutral']);
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
        $ticket = Ticket::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated description with sufficient length.',
            'category' => 'commercial',
            'sentiment' => 'positive',
            'status' => 'closed',
        ];

        $response = $this->put("/tickets/{$ticket->id}", $updatedData);

        $response->assertRedirect(); // Should redirect to show page
        $this->assertDatabaseHas('tickets', array_merge(['id' => $ticket->id], $updatedData));
    }
}
