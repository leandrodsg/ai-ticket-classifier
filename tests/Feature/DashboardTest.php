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

    /**
     * Test that dashboard uses cache for performance.
     */
    public function test_dashboard_uses_cache(): void
    {
        // Create test data
        Ticket::factory()->create(['category' => 'technical']);

        // First request should cache the data
        $startTime = microtime(true);
        $this->get('/dashboard');
        $firstRequestTime = microtime(true) - $startTime;

        // Second request should be faster due to cache
        $startTime = microtime(true);
        $this->get('/dashboard');
        $secondRequestTime = microtime(true) - $startTime;

        // Cache should make second request faster (at least 50% faster)
        $this->assertLessThan($firstRequestTime * 0.5, $secondRequestTime);

        // Verify cache key exists
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('dashboard.stats'));
    }

    /**
     * Test dashboard performance is under 100ms.
     */
    public function test_dashboard_performance_under_100ms(): void
    {
        // Create some test data
        Ticket::factory()->count(10)->create();

        $startTime = microtime(true);
        $this->get('/dashboard');
        $endTime = microtime(true);

        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $this->assertLessThan(100, $responseTime, "Dashboard response time should be under 100ms, got {$responseTime}ms");
    }
}
