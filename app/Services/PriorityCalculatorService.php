<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PriorityCalculatorService
{
    /**
     * Calculate impact level from ticket category.
     *
     * @param string $category
     * @return string
     * @throws \InvalidArgumentException
     */
    public function calculateImpact(string $category): string
    {
        $mapping = config('priority.category_to_impact');

        if (!isset($mapping[$category])) {
            Log::warning('Unknown category for impact calculation', ['category' => $category]);
            throw new \InvalidArgumentException("Unknown category: {$category}");
        }

        $impact = $mapping[$category];

        Log::info('Calculated impact from category', [
            'category' => $category,
            'impact' => $impact
        ]);

        return $impact;
    }

    /**
     * Calculate urgency level from ticket sentiment.
     *
     * @param string $sentiment
     * @return string
     * @throws \InvalidArgumentException
     */
    public function calculateUrgency(string $sentiment): string
    {
        $mapping = config('priority.sentiment_to_urgency');

        if (!isset($mapping[$sentiment])) {
            Log::warning('Unknown sentiment for urgency calculation', ['sentiment' => $sentiment]);
            throw new \InvalidArgumentException("Unknown sentiment: {$sentiment}");
        }

        $urgency = $mapping[$sentiment];

        Log::info('Calculated urgency from sentiment', [
            'sentiment' => $sentiment,
            'urgency' => $urgency
        ]);

        return $urgency;
    }

    /**
     * Calculate priority level using ITIL Impact × Urgency matrix.
     *
     * @param string $impact
     * @param string $urgency
     * @return string
     * @throws \InvalidArgumentException
     */
    public function calculatePriority(string $impact, string $urgency): string
    {
        $this->validateImpact($impact);
        $this->validateUrgency($urgency);

        $matrix = config('priority.matrix');

        if (!isset($matrix[$impact][$urgency])) {
            Log::error('Invalid Impact × Urgency combination', [
                'impact' => $impact,
                'urgency' => $urgency
            ]);
            throw new \InvalidArgumentException("Invalid Impact × Urgency combination: {$impact} × {$urgency}");
        }

        $priority = $matrix[$impact][$urgency];

        Log::info('Calculated priority from Impact × Urgency matrix', [
            'impact' => $impact,
            'urgency' => $urgency,
            'priority' => $priority
        ]);

        return $priority;
    }

    /**
     * Calculate complete priority data from category and sentiment.
     *
     * @param string $category
     * @param string $sentiment
     * @return array
     * @throws \InvalidArgumentException
     */
    public function calculateFromCategoryAndSentiment(string $category, string $sentiment): array
    {
        $impact = $this->calculateImpact($category);
        $urgency = $this->calculateUrgency($sentiment);
        $priority = $this->calculatePriority($impact, $urgency);

        return [
            'priority' => $priority,
            'impact_level' => $impact,
            'urgency_level' => $urgency,
        ];
    }

    /**
     * Validate that impact level is valid.
     *
     * @param string $impact
     * @throws \InvalidArgumentException
     */
    protected function validateImpact(string $impact): void
    {
        $validImpacts = config('priority.valid_impacts');

        if (!in_array($impact, $validImpacts)) {
            throw new \InvalidArgumentException("Invalid impact level: {$impact}. Valid: " . implode(', ', $validImpacts));
        }
    }

    /**
     * Validate that urgency level is valid.
     *
     * @param string $urgency
     * @throws \InvalidArgumentException
     */
    protected function validateUrgency(string $urgency): void
    {
        $validUrgencies = config('priority.valid_urgencies');

        if (!in_array($urgency, $validUrgencies)) {
            throw new \InvalidArgumentException("Invalid urgency level: {$urgency}. Valid: " . implode(', ', $validUrgencies));
        }
    }

    /**
     * Validate that priority level is valid.
     *
     * @param string $priority
     * @throws \InvalidArgumentException
     */
    public function validatePriority(string $priority): void
    {
        $validPriorities = config('priority.valid_priorities');

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid priority level: {$priority}. Valid: " . implode(', ', $validPriorities));
        }
    }
}
