<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that dashboard view loads successfully.
     */
    public function test_dashboard_view_loads(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }

    /**
     * Test that dashboard returns correct data structure.
     */
    public function test_dashboard_returns_correct_data(): void
    {
        // Create test tickets
        Ticket::factory()->create(['category' => 'technical', 'sentiment' => 'positive', 'status' => 'open']);
        Ticket::factory()->create(['category' => 'commercial', 'sentiment' => 'negative', 'status' => 'closed']);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);

        // Check that view has the expected data
        $response->assertViewHasAll([
            'totalTickets',
            'ticketsByCategory',
            'ticketsBySentiment',
            'ticketsByStatus'
        ]);

        // Verify data types
        $viewData = $response->getOriginalContent()->getData();
        $this->assertIsInt($viewData['totalTickets']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $viewData['ticketsByCategory']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $viewData['ticketsBySentiment']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $viewData['ticketsByStatus']);
    }
}
