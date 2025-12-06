<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_is_required(): void
    {
        $response = $this->post('/tickets', [
            'description' => 'Valid description with more than 10 characters for testing purposes.',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title_minimum_length(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Hi', // Less than 5 characters
            'description' => 'Valid description with more than 10 characters for testing purposes.',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title_maximum_length(): void
    {
        $response = $this->post('/tickets', [
            'title' => str_repeat('A', 256), // More than 255 characters
            'description' => 'Valid description with more than 10 characters for testing purposes.',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_description_is_required(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_description_minimum_length(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => 'Short', // Less than 10 characters
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_description_maximum_length(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => str_repeat('A', 5001), // More than 5000 characters
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_category_maximum_length(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => 'Valid description with more than 10 characters for testing purposes.',
            'category' => str_repeat('A', 101), // More than 100 characters
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_sentiment_must_be_valid_enum(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => 'Valid description with more than 10 characters for testing purposes.',
            'sentiment' => 'invalid_sentiment',
        ]);

        $response->assertSessionHasErrors('sentiment');
    }

    public function test_status_must_be_valid_enum(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => 'Valid description with more than 10 characters for testing purposes.',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_category_accepts_valid_string(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Valid Title',
            'description' => 'Valid description with more than 10 characters for testing purposes.',
            'category' => 'technical',
        ]);

        // Should not have validation errors for category
        $response->assertSessionDoesntHaveErrors('category');
    }
}
