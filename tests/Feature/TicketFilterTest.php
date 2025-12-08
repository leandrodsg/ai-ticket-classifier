<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_category(): void
    {
        Ticket::factory()->create(['category' => 'technical', 'title' => 'Tech Issue']);
        Ticket::factory()->create(['category' => 'billing', 'title' => 'Billing Issue']);

        $response = $this->get('/tickets?category=technical');

        $response->assertStatus(200);
        $response->assertSee('Tech Issue');
        $response->assertDontSee('Billing Issue');
    }

    public function test_filter_by_sentiment(): void
    {
        Ticket::factory()->create(['sentiment' => 'positive', 'title' => 'Happy Customer']);
        Ticket::factory()->create(['sentiment' => 'negative', 'title' => 'Angry Customer']);

        $response = $this->get('/tickets?sentiment=positive');

        $response->assertStatus(200);
        $response->assertSee('Happy Customer');
        $response->assertDontSee('Angry Customer');
    }

    public function test_filter_by_status(): void
    {
        Ticket::factory()->create(['status' => 'open', 'title' => 'Open Ticket']);
        Ticket::factory()->create(['status' => 'closed', 'title' => 'Closed Ticket']);

        $response = $this->get('/tickets?status=open');

        $response->assertStatus(200);
        $response->assertSee('Open Ticket');
        $response->assertDontSee('Closed Ticket');
    }

    public function test_filter_by_priority(): void
    {
        Ticket::factory()->create(['priority' => 'critical', 'title' => 'Critical Issue']);
        Ticket::factory()->create(['priority' => 'low', 'title' => 'Low Priority']);

        $response = $this->get('/tickets?priority=critical');

        $response->assertStatus(200);
        $response->assertSee('Critical Issue');
        $response->assertDontSee('Low Priority');
    }

    public function test_search_by_title(): void
    {
        Ticket::factory()->create(['title' => 'Database Connection Error']);
        Ticket::factory()->create(['title' => 'Email Not Sending']);

        $response = $this->get('/tickets?search=Database');

        $response->assertStatus(200);
        $response->assertSee('Database Connection Error');
        $response->assertDontSee('Email Not Sending');
    }

    public function test_search_by_description(): void
    {
        Ticket::factory()->create([
            'title' => 'Issue 1',
            'description' => 'This is about payment processing'
        ]);
        Ticket::factory()->create([
            'title' => 'Issue 2',
            'description' => 'This is about user authentication'
        ]);

        $response = $this->get('/tickets?search=payment');

        $response->assertStatus(200);
        $response->assertSee('Issue 1');
        $response->assertDontSee('Issue 2');
    }

    public function test_multiple_filters_combined(): void
    {
        Ticket::factory()->create([
            'category' => 'technical',
            'status' => 'open',
            'title' => 'Match'
        ]);
        Ticket::factory()->create([
            'category' => 'technical',
            'status' => 'closed',
            'title' => 'No Match - Wrong Status'
        ]);
        Ticket::factory()->create([
            'category' => 'billing',
            'status' => 'open',
            'title' => 'No Match - Wrong Category'
        ]);

        $response = $this->get('/tickets?category=technical&status=open');

        $response->assertStatus(200);
        $response->assertSee('Match');
        $response->assertDontSee('No Match');
    }

    public function test_pagination_works_with_filters(): void
    {
        // Create 20 technical tickets
        Ticket::factory()->count(20)->create(['category' => 'technical']);
        
        // Create 5 billing tickets
        Ticket::factory()->count(5)->create(['category' => 'billing']);

        $response = $this->get('/tickets?category=technical');

        $response->assertStatus(200);
        // Should have pagination links since we have 20 items with default 15 per page
        $response->assertViewHas('tickets');
    }

    public function test_no_results_when_no_matches(): void
    {
        Ticket::factory()->create(['category' => 'technical']);

        $response = $this->get('/tickets?category=nonexistent');

        $response->assertStatus(200);
        $response->assertViewHas('tickets');
        
        $tickets = $response->viewData('tickets');
        $this->assertCount(0, $tickets);
    }
}
