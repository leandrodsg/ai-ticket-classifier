<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 25 sample tickets with realistic data
        Ticket::factory()->create([
            'title' => 'Login system completely broken',
            'description' => 'Since the latest update, I cannot log into the system at all. The login button doesn\'t respond and I get a JavaScript error in the console. This is affecting my ability to work completely.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Need enterprise pricing information',
            'description' => 'Our company is growing rapidly and we need to upgrade to the enterprise plan. Could you provide detailed pricing for 100+ users including all available features and support options?',
            'category' => 'commercial',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Charged twice for subscription',
            'description' => 'I was billed twice for my monthly subscription. Transaction IDs are #TXN-2025-001 and #TXN-2025-002. Please refund the duplicate charge and prevent this from happening again.',
            'category' => 'billing',
            'sentiment' => 'negative',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'How do I export my data?',
            'description' => 'I need to export all my tickets and customer data for backup purposes. Could you guide me through the export process or tell me where to find this feature?',
            'category' => 'general',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Excellent customer support experience',
            'description' => 'I had an issue with my account and the support team resolved it within minutes. The response was fast, professional, and very helpful. Thank you for the great service!',
            'category' => 'support',
            'sentiment' => 'positive',
            'status' => 'closed',
        ]);

        Ticket::factory()->create([
            'title' => 'Database performance issues',
            'description' => 'The system is extremely slow when loading ticket lists. Queries are taking 10+ seconds to complete. This is severely impacting productivity. Please investigate database performance.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Custom integration pricing request',
            'description' => 'We need to integrate your platform with our existing CRM system. This would require custom API development. Please provide pricing estimates for this integration work.',
            'category' => 'commercial',
            'sentiment' => 'neutral',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'Refund processed successfully',
            'description' => 'Thank you for processing my refund request quickly. The refund for the cancelled subscription has been credited back to my account. Excellent service!',
            'category' => 'billing',
            'sentiment' => 'positive',
            'status' => 'closed',
        ]);

        Ticket::factory()->create([
            'title' => 'API documentation request',
            'description' => 'I am a developer looking to integrate with your API. Could you provide comprehensive API documentation including authentication methods and available endpoints?',
            'category' => 'general',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'System crashes on file upload',
            'description' => 'Whenever I try to upload a file larger than 10MB, the entire application crashes. This happens consistently across different browsers and file types.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Volume licensing discount inquiry',
            'description' => 'We have 200 employees who need access to the platform. Do you offer volume licensing discounts? If so, what are the pricing tiers for large organizations?',
            'category' => 'commercial',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Billing address update',
            'description' => 'I need to update my billing address for tax invoice purposes. The current address on file is outdated. Please provide instructions on how to update this information.',
            'category' => 'billing',
            'sentiment' => 'neutral',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'Training materials for new users',
            'description' => 'We have onboarded 15 new team members who need training. Could you provide access to training materials, video tutorials, or arrange a training session?',
            'category' => 'general',
            'sentiment' => 'positive',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Email notifications not working',
            'description' => 'I am not receiving any email notifications when tickets are assigned to me or when there are updates. I have checked my spam folder and email settings are correct.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Annual subscription discount',
            'description' => 'I would like to switch from monthly to annual billing. Are there any discounts available for annual subscriptions? Please provide the pricing comparison.',
            'category' => 'commercial',
            'sentiment' => 'neutral',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'Invoice payment confirmation',
            'description' => 'I made a payment for invoice #INV-2025-045 but haven\'t received confirmation yet. Could you verify that the payment was processed successfully?',
            'category' => 'billing',
            'sentiment' => 'neutral',
            'status' => 'closed',
        ]);

        Ticket::factory()->create([
            'title' => 'Mobile app feature request',
            'description' => 'It would be great to have a mobile app for iOS and Android. This would allow us to manage tickets on the go. Is this feature planned for development?',
            'category' => 'general',
            'sentiment' => 'positive',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'API rate limiting too restrictive',
            'description' => 'The API rate limits are too low for our use case. We need to make more API calls per minute. Could you increase our rate limits or provide information about premium API tiers?',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'Custom reporting features',
            'description' => 'We need advanced reporting capabilities including custom date ranges, user activity reports, and SLA compliance metrics. Is this available in higher-tier plans?',
            'category' => 'commercial',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Credit card payment failed',
            'description' => 'My credit card payment failed during checkout. The error message said "Card declined". I have sufficient funds and the card details are correct. Please investigate.',
            'category' => 'billing',
            'sentiment' => 'negative',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'System compatibility question',
            'description' => 'We are planning to upgrade our infrastructure. Could you confirm that the platform is compatible with Windows Server 2025 and SQL Server 2022?',
            'category' => 'general',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'User authentication failing',
            'description' => 'Multiple users are reporting authentication issues. They can log in successfully but get logged out randomly after a few minutes. This is affecting multiple team members.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Premium plan upgrade inquiry',
            'description' => 'We are considering upgrading from the professional plan to premium. What additional features are included and what is the price difference?',
            'category' => 'commercial',
            'sentiment' => 'positive',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Tax invoice request',
            'description' => 'I need a tax invoice for my recent subscription purchase. Please send it to my accounting department with all required tax information and company details.',
            'category' => 'billing',
            'sentiment' => 'neutral',
            'status' => 'closed',
        ]);

        Ticket::factory()->create([
            'title' => 'Best practices guide request',
            'description' => 'Could you provide a comprehensive guide on best practices for ticket management, customer communication, and team collaboration within the platform?',
            'category' => 'support',
            'sentiment' => 'positive',
            'status' => 'open',
        ]);

        Ticket::factory()->create([
            'title' => 'Application crashes on startup',
            'description' => 'The application crashes immediately on startup. I see a brief loading screen and then it closes. This happens on both Windows 10 and Windows 11 machines.',
            'category' => 'technical',
            'sentiment' => 'negative',
            'status' => 'pending',
        ]);

        Ticket::factory()->create([
            'title' => 'Subscription auto-renewal issue',
            'description' => 'My subscription is set to auto-renew but I received a notice that it will expire soon. Please check the auto-renewal settings and ensure it is properly configured.',
            'category' => 'billing',
            'sentiment' => 'neutral',
            'status' => 'open',
        ]);

        // Generate 5 additional random tickets using the factory
        Ticket::factory(5)->create();
    }
}
