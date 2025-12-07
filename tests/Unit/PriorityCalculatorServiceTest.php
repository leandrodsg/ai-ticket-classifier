<?php

namespace Tests\Unit;

use App\Services\PriorityCalculatorService;
use Tests\TestCase;

class PriorityCalculatorServiceTest extends TestCase
{
    private PriorityCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PriorityCalculatorService();
    }

    public function test_calculate_impact_from_category()
    {
        // Test valid categories
        $this->assertEquals('critical', $this->service->calculateImpact('technical'));
        $this->assertEquals('high', $this->service->calculateImpact('billing'));
        $this->assertEquals('medium', $this->service->calculateImpact('commercial'));
        $this->assertEquals('low', $this->service->calculateImpact('general'));
        $this->assertEquals('low', $this->service->calculateImpact('support'));
    }

    public function test_calculate_impact_invalid_category()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown category: invalid');
        $this->service->calculateImpact('invalid');
    }

    public function test_calculate_urgency_from_sentiment()
    {
        // Test valid sentiments
        $this->assertEquals('high', $this->service->calculateUrgency('negative'));
        $this->assertEquals('medium', $this->service->calculateUrgency('neutral'));
        $this->assertEquals('low', $this->service->calculateUrgency('positive'));
    }

    public function test_calculate_urgency_invalid_sentiment()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown sentiment: invalid');
        $this->service->calculateUrgency('invalid');
    }

    public function test_calculate_priority_itil_matrix()
    {
        // Test all combinations from ITIL matrix
        $this->assertEquals('critical', $this->service->calculatePriority('critical', 'high'));
        $this->assertEquals('critical', $this->service->calculatePriority('critical', 'medium'));
        $this->assertEquals('high', $this->service->calculatePriority('critical', 'low'));

        $this->assertEquals('critical', $this->service->calculatePriority('high', 'high'));
        $this->assertEquals('high', $this->service->calculatePriority('high', 'medium'));
        $this->assertEquals('medium', $this->service->calculatePriority('high', 'low'));

        $this->assertEquals('high', $this->service->calculatePriority('medium', 'high'));
        $this->assertEquals('medium', $this->service->calculatePriority('medium', 'medium'));
        $this->assertEquals('low', $this->service->calculatePriority('medium', 'low'));

        $this->assertEquals('medium', $this->service->calculatePriority('low', 'high'));
        $this->assertEquals('low', $this->service->calculatePriority('low', 'medium'));
        $this->assertEquals('low', $this->service->calculatePriority('low', 'low'));
    }

    public function test_calculate_priority_invalid_impact()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid impact level: invalid');
        $this->service->calculatePriority('invalid', 'high');
    }

    public function test_calculate_priority_invalid_urgency()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid urgency level: invalid');
        $this->service->calculatePriority('critical', 'invalid');
    }

    public function test_calculate_from_category_and_sentiment()
    {
        $result = $this->service->calculateFromCategoryAndSentiment('technical', 'negative');

        $this->assertEquals([
            'priority' => 'critical',
            'impact_level' => 'critical',
            'urgency_level' => 'high',
        ], $result);
    }

    public function test_calculate_from_category_and_sentiment_invalid_category()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->calculateFromCategoryAndSentiment('invalid', 'negative');
    }

    public function test_calculate_from_category_and_sentiment_invalid_sentiment()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->calculateFromCategoryAndSentiment('technical', 'invalid');
    }

    public function test_validate_priority()
    {
        // Valid priorities should not throw exceptions
        $this->service->validatePriority('critical');
        $this->service->validatePriority('high');
        $this->service->validatePriority('medium');
        $this->service->validatePriority('low');

        // Invalid priority should throw exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid priority level: invalid');
        $this->service->validatePriority('invalid');
    }

    public function test_real_world_scenarios()
    {
        // Critical: Technical issue with negative sentiment
        $result = $this->service->calculateFromCategoryAndSentiment('technical', 'negative');
        $this->assertEquals('critical', $result['priority']);

        // High: Billing issue with neutral sentiment
        $result = $this->service->calculateFromCategoryAndSentiment('billing', 'neutral');
        $this->assertEquals('high', $result['priority']);

        // Low: Commercial inquiry with positive sentiment
        $result = $this->service->calculateFromCategoryAndSentiment('commercial', 'positive');
        $this->assertEquals('low', $result['priority']);

        // Low: General support with neutral sentiment
        $result = $this->service->calculateFromCategoryAndSentiment('general', 'neutral');
        $this->assertEquals('low', $result['priority']);
    }
}
