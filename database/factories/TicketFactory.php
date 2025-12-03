<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraphs(2, true),
            'category' => fake()->randomElement(['technical', 'commercial', 'billing', 'general']),
            'sentiment' => fake()->randomElement(['positive', 'negative', 'neutral']),
            'status' => fake()->randomElement(['open', 'closed', 'pending']),
        ];
    }
}
