<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->delete("/tickets/{$ticket->id}");

        $response->assertRedirect(route('tickets.index'));
        $response->assertSessionHas('success', 'Ticket deleted successfully!');

        // Verify ticket was soft deleted
        $this->assertSoftDeleted('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_deleted_ticket_not_visible_in_index(): void
    {
        $activeTicket = Ticket::factory()->create(['title' => 'Active Ticket']);
        $deletedTicket = Ticket::factory()->create(['title' => 'Deleted Ticket']);
        
        $deletedTicket->delete();

        $response = $this->get('/tickets');

        $response->assertStatus(200);
        $response->assertSee('Active Ticket');
        $response->assertDontSee('Deleted Ticket');
    }

    public function test_cannot_delete_nonexistent_ticket(): void
    {
        $response = $this->delete('/tickets/99999');

        $response->assertStatus(404);
    }
}
