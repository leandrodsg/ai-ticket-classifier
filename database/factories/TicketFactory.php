<?php

namespace Database\Factories;

use App\Services\TicketClassifierService;
use App\Services\SlaCalculatorService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

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
        $category = fake()->randomElement(['technical', 'commercial', 'billing', 'general', 'support']);

        // Generate description based on category
        $description = $this->generateDescriptionForCategory($category);

        $data = [
            'title' => $this->generateTitleForCategory($category),
            'description' => $description,
            'category' => $category,
            'sentiment' => fake()->randomElement(['positive', 'negative', 'neutral']),
            'status' => fake()->randomElement(['open', 'closed', 'pending']),
        ];

        // Try to classify with AI and add priority
        try {
            $classifier = app(TicketClassifierService::class);
            $classification = $classifier->classifyWithPriority($description);

            // Calculate SLA due date
            $slaCalculator = app(SlaCalculatorService::class);
            $slaDueAt = isset($classification['priority'])
                ? $slaCalculator->calculateDueDate($classification['priority'], now())
                : null;

            // Merge AI classification data
            $data = array_merge($data, [
                'category' => $classification['category'],
                'sentiment' => $classification['sentiment'],
                'confidence' => $classification['confidence'],
                'priority' => $classification['priority'] ?? null,
                'impact_level' => $classification['impact_level'] ?? null,
                'urgency_level' => $classification['urgency_level'] ?? null,
                'sla_due_at' => $slaDueAt,
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail factory creation
            Log::error('AI classification failed during factory creation', [
                'description' => $description,
                'error' => $e->getMessage(),
            ]);

            // Keep the manually set category and sentiment, but no priority
            $data['priority'] = null;
            $data['impact_level'] = null;
            $data['urgency_level'] = null;
            $data['sla_due_at'] = null;
        }

        return $data;
    }

    /**
     * Generate a realistic title based on category.
     */
    private function generateTitleForCategory(string $category): string
    {
        $titles = [
            'technical' => [
                'Login system not working properly',
                'Database connection timeout error',
                'Application crashes on startup',
                'File upload feature broken',
                'Email notifications not sending',
                'System performance is very slow',
                'API endpoint returning 500 error',
                'User authentication failing',
            ],
            'commercial' => [
                'Need pricing information for enterprise plan',
                'Question about subscription features',
                'Request for custom pricing quote',
                'Billing cycle change request',
                'Upgrade to premium plan inquiry',
                'Discount for annual subscription',
                'Custom integration pricing',
                'Volume licensing options',
            ],
            'billing' => [
                'Incorrect charge on my account',
                'Refund request for cancelled service',
                'Billing address update needed',
                'Invoice payment confirmation',
                'Subscription auto-renewal issue',
                'Credit card payment failed',
                'Tax invoice request',
                'Billing cycle clarification',
            ],
            'general' => [
                'How do I reset my password?',
                'Where can I find the user manual?',
                'Contact information for support',
                'Feature request for mobile app',
                'Account settings help needed',
                'How to export my data',
                'Training materials request',
                'System compatibility question',
            ],
            'support' => [
                'General assistance needed',
                'How to get started with the platform',
                'Best practices for using the system',
                'Troubleshooting guide request',
                'System requirements check',
                'User onboarding support',
                'General platform questions',
                'Help with basic features',
            ],
        ];

        return fake()->randomElement($titles[$category] ?? $titles['general']);
    }

    /**
     * Generate a realistic description based on category.
     */
    private function generateDescriptionForCategory(string $category): string
    {
        $descriptions = [
            'technical' => [
                'I am experiencing issues with the login system. When I try to enter my credentials, the system shows an error message and I cannot access my account. This has been happening for the past 2 days.',
                'The application is running very slowly and sometimes crashes completely. This started after the latest update. I have tried restarting my computer but the problem persists.',
                'I cannot upload files to the system. Every time I try to attach a document, I get a timeout error. The files are not too large (under 5MB) so this should not be an issue.',
                'The email notification system is not working. I am not receiving any emails when tickets are updated or when I get assigned new tasks. Please check the email configuration.',
            ],
            'commercial' => [
                'I am interested in upgrading to your enterprise plan. Could you please provide detailed pricing information and compare it with the current professional plan?',
                'We are a growing startup and need to understand your volume licensing options. Do you offer discounts for multiple user accounts?',
                'I would like to request a custom quote for our specific needs. We have 50 users and need advanced reporting features. Please contact me with pricing details.',
                'Can you explain the differences between your subscription tiers? I need to know which plan would be most suitable for a team of 25 developers.',
            ],
            'billing' => [
                'I was charged twice for the same subscription period. Please check transaction ID #12345 and process a refund for the duplicate charge.',
                'I cancelled my subscription last month but I am still being charged. Please stop the recurring billing and refund the last payment.',
                'My credit card expired and the automatic payment failed. I have updated my card details but need to know when the next billing cycle will process.',
                'I need a tax invoice for my recent purchase. Please send it to my accounting department with all the necessary tax details.',
            ],
            'general' => [
                'I am new to the platform and need help getting started. Could you provide a quick guide or tutorial on how to set up my account and begin using the basic features?',
                'Where can I find the complete user manual? I need detailed instructions for advanced features like custom reporting and API integrations.',
                'I would like to request training materials for my team. We have 10 new users who need to learn how to use the system effectively.',
                'Can you help me understand the system requirements? I want to make sure my current setup is compatible before purchasing.',
            ],
            'support' => [
                'I need general assistance with the platform. I am having trouble understanding how to navigate between different sections and access my data.',
                'Could you provide guidance on best practices for organizing tickets and managing customer communications within the system?',
                'I am looking for troubleshooting guides. The system occasionally shows error messages and I need to know how to resolve common issues.',
                'Please help me with the onboarding process. I have created my account but am unsure about the next steps to set up my workspace properly.',
            ],
        ];

        return fake()->randomElement($descriptions[$category] ?? $descriptions['general']);
    }
}
