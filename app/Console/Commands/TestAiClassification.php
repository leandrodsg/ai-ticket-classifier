<?php

namespace App\Console\Commands;

use App\Services\TicketClassifierService;
use Illuminate\Console\Command;

class TestAiClassification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test-classification {description?} {--mode=mock : Classification mode (mock or real)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AI ticket classification with mock or real DeepSeek API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $description = $this->argument('description') ?: $this->ask('Enter ticket description to classify');
        $mode = $this->option('mode');

        $this->info("🔍 Testing AI Classification");
        $this->line("📝 Description: {$description}");
        $this->line("🎯 Mode: {$mode}");
        $this->line('');

        // Temporarily set mock mode if specified
        $originalMockMode = config('ai.global.always_use_mock');
        if ($mode === 'mock') {
            config(['ai.global.always_use_mock' => true]);
        } elseif ($mode === 'real') {
            config(['ai.global.always_use_mock' => false]);
        }

        $service = app(TicketClassifierService::class);

        $this->info("🤖 Classifying...");
        $startTime = microtime(true);

        try {
            // Test both basic classification and classification with priority
            $result = $service->classify($description);
            $resultWithPriority = $service->classifyWithPriority($description);
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->line('');
            $this->info("✅ Basic Classification Result:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Category', $result['category'] ?? 'N/A'],
                    ['Sentiment', $result['sentiment'] ?? 'N/A'],
                    ['Confidence', isset($result['confidence']) ? number_format($result['confidence'], 3) : 'N/A'],
                    ['Model', $result['model'] ?? 'N/A'],
                    ['Processing Time', "{$processingTime}ms"],
                    ['Reasoning', $result['reasoning'] ?? 'N/A'],
                ]
            );

            $this->line('');
            $this->info("🎯 Classification with Priority Result:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Category', $resultWithPriority['category'] ?? 'N/A'],
                    ['Sentiment', $resultWithPriority['sentiment'] ?? 'N/A'],
                    ['Priority', $resultWithPriority['priority'] ?? 'NULL'],
                    ['Impact Level', $resultWithPriority['impact_level'] ?? 'NULL'],
                    ['Urgency Level', $resultWithPriority['urgency_level'] ?? 'NULL'],
                    ['Confidence', isset($resultWithPriority['confidence']) ? number_format($resultWithPriority['confidence'], 3) : 'N/A'],
                    ['Model', $resultWithPriority['model'] ?? 'N/A'],
                ]
            );

            if (isset($result['processing_time_ms'])) {
                $this->info("⚡ API Response Time: {$result['processing_time_ms']}ms");
            }

        } catch (\Exception $e) {
            $this->error("❌ Classification failed: {$e->getMessage()}");

            $this->line("🔄 Falling back to mock classification...");

            try {
                config(['ai.global.always_use_mock' => true]);
                $fallbackResult = $service->classify($description);

                $this->line('');
                $this->warn("🔄 Fallback Result (Mock Mode):");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Category', $fallbackResult['category'] ?? 'N/A'],
                        ['Sentiment', $fallbackResult['sentiment'] ?? 'N/A'],
                        ['Confidence', isset($fallbackResult['confidence']) ? number_format($fallbackResult['confidence'], 3) : 'N/A'],
                        ['Model', $fallbackResult['model'] ?? 'N/A'],
                    ]
                );
            } catch (\Exception $fallbackError) {
                $this->error("❌ Fallback also failed: {$fallbackError->getMessage()}");
            }
        }

        // Restore original mock mode
        config(['ai.global.always_use_mock' => $originalMockMode]);

        $this->line('');
        $this->info("🎯 Test completed!");
    }
}
