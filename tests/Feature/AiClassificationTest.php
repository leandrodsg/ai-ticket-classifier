<?php

namespace Tests\Feature;

use App\Services\TicketClassifierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiClassificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_service_returns_valid_classification(): void
    {
        $service = app(TicketClassifierService::class);

        $description = 'Customer reporting login error in the system';
        $result = $service->classify($description);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('sentiment', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('model', $result);

        $this->assertContains($result['category'], ['technical', 'commercial', 'billing', 'general', 'support']);
        $this->assertContains($result['sentiment'], ['positive', 'negative', 'neutral']);
        $this->assertIsNumeric($result['confidence']);
        $this->assertGreaterThanOrEqual(0, $result['confidence']);
        $this->assertLessThanOrEqual(1, $result['confidence']);
    }

    public function test_mock_classification_always_works(): void
    {
        // Force mock mode
        config(['ai.global.always_use_mock' => true]);

        $service = app(TicketClassifierService::class);

        $description = 'Any description here';
        $result = $service->classify($description);

        $this->assertIsArray($result);
        $this->assertEquals('mock-classifier', $result['model']);
        $this->assertContains($result['category'], ['technical', 'commercial', 'billing', 'general', 'support']);
        $this->assertContains($result['sentiment'], ['positive', 'negative', 'neutral']);
    }
}
