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

        // Clear any existing cache
        \Illuminate\Support\Facades\Cache::forget('dashboard.stats');

        // First request should create cache
        $this->get('/dashboard');

        // Verify cache key exists after first request
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('dashboard.stats'));

        // Second request should use cache
        $this->get('/dashboard');

        // Cache should still exist
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

    /**
     * Test that API endpoint has rate limiting.
     */
    public function test_api_endpoint_has_rate_limiting(): void
    {
        // Make 30 requests (should be ok - matches throttle:30,1 limit)
        for ($i = 0; $i < 30; $i++) {
            $response = $this->get('/api/dashboard/stats');
            $response->assertStatus(200);
        }

        // 31st request should be rate limited
        $response = $this->get('/api/dashboard/stats');
        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test that API returns JSON data.
     */
    public function test_api_returns_json_data(): void
    {
        // Create test data
        Ticket::factory()->create(['category' => 'technical']);

        $response = $this->get('/api/dashboard/stats');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'application/json');

        $data = $response->json();

        // Verify JSON structure
        $this->assertArrayHasKey('totalTickets', $data);
        $this->assertArrayHasKey('ticketsByCategory', $data);
        $this->assertArrayHasKey('ticketsBySentiment', $data);
        $this->assertArrayHasKey('ticketsByStatus', $data);

        $this->assertIsInt($data['totalTickets']);
        $this->assertIsArray($data['ticketsByCategory']);
    }
}
