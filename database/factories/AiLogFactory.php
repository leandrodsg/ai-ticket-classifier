<?php

namespace Database\Factories;

use App\Models\AiLog;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiLog>
 */
class AiLogFactory extends Factory
{
    protected $model = AiLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'model' => fake()->randomElement([
                'gpt-4',
                'gpt-3.5-turbo',
                'meta-llama/llama-3.3-70b-instruct:free',
                'claude-3-opus'
            ]),
            'prompt' => fake()->sentence(20),
            'response' => [
                'category' => fake()->randomElement(['technical', 'billing', 'commercial', 'support', 'general']),
                'sentiment' => fake()->randomElement(['positive', 'negative', 'neutral']),
                'confidence' => fake()->randomFloat(2, 0.7, 0.99),
                'reasoning' => fake()->sentence(30),
            ],
            'confidence' => fake()->randomFloat(2, 0.7, 0.99),
            'processing_time_ms' => fake()->numberBetween(50, 5000),
            'status' => fake()->randomElement(['success', 'error']),
            'error_message' => null,
        ];
    }

    /**
     * Indicate that the AI log is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the AI log failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'error',
            'error_message' => fake()->sentence(),
            'response' => null,
        ]);
    }
}
