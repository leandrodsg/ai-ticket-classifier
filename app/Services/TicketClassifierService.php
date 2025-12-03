<?php

namespace App\Services;

use App\Models\AiLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TicketClassifierService
{
    /**
     * Classify a ticket description using AI.
     *
     * @param string $description
     * @return array
     * @throws \Exception
     */
    public function classify(string $description): array
    {
        // Check if mock mode is enabled
        if (config('ai.deepseek.mock_mode')) {
            return $this->mockClassify($description);
        }

        return $this->realClassify($description);
    }

    /**
     * Classify using DeepSeek API.
     *
     * @param string $description
     * @return array
     * @throws \Exception
     */
    protected function realClassify(string $description): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(config('ai.deepseek.timeout'))
                ->retry(
                    config('ai.deepseek.retries'),
                    config('ai.deepseek.retry_delay'),
                    function ($exception) {
                        return $exception instanceof ConnectionException ||
                               $exception instanceof RequestException;
                    }
                )
                ->withToken(config('ai.deepseek.api_key'))
                ->post(config('ai.deepseek.api_url') . '/chat/completions', [
                    'model' => config('ai.deepseek.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $description
                        ]
                    ],
                    'temperature' => config('ai.deepseek.temperature'),
                    'max_tokens' => config('ai.deepseek.max_tokens'),
                ]);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return $this->parseApiResponse($response->json(), $processingTime);
            }

            throw new \Exception("API request failed: {$response->status()} - {$response->body()}");

        } catch (\Exception $e) {
            Log::error('DeepSeek API classification failed', [
                'description' => $description,
                'error' => $e->getMessage(),
                'processing_time_ms' => (int) ((microtime(true) - $startTime) * 1000)
            ]);

            // Fallback to mock classification if API fails
            return $this->mockClassify($description);
        }
    }

    /**
     * Mock classification for development/testing.
     *
     * @param string $description
     * @return array
     */
    public function mockClassify(string $description): array
    {
        $lower = strtolower($description);

        // Category detection based on keywords
        $category = $this->detectCategory($lower);

        // Sentiment analysis
        $sentiment = $this->detectSentiment($lower);

        // Generate confidence based on keyword matches
        $confidence = $this->calculateConfidence($lower);

        return [
            'category' => $category,
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'reasoning' => "Mock classification based on keyword analysis",
            'model' => 'mock-classifier',
        ];
    }

    /**
     * Get the system prompt for AI classification.
     *
     * @return string
     */
    protected function getSystemPrompt(): string
    {
        $categories = implode(', ', config('ai.deepseek.valid_categories'));
        $sentiments = implode(', ', config('ai.deepseek.valid_sentiments'));

        return "You are an AI assistant that classifies customer support tickets. " .
               "Analyze the ticket description and return a JSON response with: " .
               "category ({$categories}), sentiment ({$sentiments}), confidence (0.0-1.0), " .
               "and reasoning. Be precise and consistent.";
    }

    /**
     * Parse the API response from DeepSeek.
     *
     * @param array $response
     * @param int $processingTime
     * @return array
     */
    protected function parseApiResponse(array $response, int $processingTime): array
    {
        // Extract the content from the response
        $content = $response['choices'][0]['message']['content'] ?? '';

        // Try to parse JSON from the content
        $parsed = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response from AI: {$content}");
        }

        // Validate the response structure
        $this->validateClassificationResponse($parsed);

        return array_merge($parsed, [
            'processing_time_ms' => $processingTime,
            'model' => $response['model'] ?? config('ai.deepseek.model'),
        ]);
    }

    /**
     * Validate the classification response structure.
     *
     * @param array $response
     * @throws \Exception
     */
    protected function validateClassificationResponse(array $response): void
    {
        $required = ['category', 'sentiment', 'confidence'];

        foreach ($required as $field) {
            if (!isset($response[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        if (!in_array($response['category'], config('ai.deepseek.valid_categories'))) {
            throw new \Exception("Invalid category: {$response['category']}");
        }

        if (!in_array($response['sentiment'], config('ai.deepseek.valid_sentiments'))) {
            throw new \Exception("Invalid sentiment: {$response['sentiment']}");
        }

        if (!is_numeric($response['confidence']) || $response['confidence'] < 0 || $response['confidence'] > 1) {
            throw new \Exception("Invalid confidence value: {$response['confidence']}");
        }
    }

    /**
     * Detect category based on keywords.
     *
     * @param string $text
     * @return string
     */
    protected function detectCategory(string $text): string
    {
        // Technical keywords
        if (preg_match('/\b(bug|error|crash|fail|broken|not working|doesn\'?t work)\b/i', $text)) {
            return 'technical';
        }

        // Commercial keywords
        if (preg_match('/\b(price|cost|buy|purchase|plan|subscription|quote)\b/i', $text)) {
            return 'commercial';
        }

        // Billing keywords
        if (preg_match('/\b(bill|invoice|payment|charge|refund|money)\b/i', $text)) {
            return 'billing';
        }

        // Support keywords (default)
        return 'support';
    }

    /**
     * Detect sentiment based on keywords.
     *
     * @param string $text
     * @return string
     */
    protected function detectSentiment(string $text): string
    {
        // Negative keywords
        if (preg_match('/\b(urgent|problem|issue|frustrated|angry|disappointed|terrible|awful)\b/i', $text)) {
            return 'negative';
        }

        // Positive keywords
        if (preg_match('/\b(thank|great|excellent|amazing|happy|pleased|awesome|love)\b/i', $text)) {
            return 'positive';
        }

        // Neutral (default)
        return 'neutral';
    }

    /**
     * Calculate confidence based on keyword matches.
     *
     * @param string $text
     * @return float
     */
    protected function calculateConfidence(string $text): float
    {
        $matches = 0;
        $total = 0;

        // Category keywords
        $total += 4;
        if (preg_match('/\b(bug|error|crash|fail|broken|not working)\b/i', $text)) $matches++;
        if (preg_match('/\b(price|cost|buy|purchase|plan)\b/i', $text)) $matches++;
        if (preg_match('/\b(bill|invoice|payment|charge)\b/i', $text)) $matches++;
        if (preg_match('/\b(help|support|question|how|what)\b/i', $text)) $matches++;

        // Sentiment keywords
        $total += 3;
        if (preg_match('/\b(urgent|problem|issue|frustrated|angry)\b/i', $text)) $matches++;
        if (preg_match('/\b(thank|great|excellent|amazing|happy)\b/i', $text)) $matches++;
        if (preg_match('/\b(please|could|would|can)\b/i', $text)) $matches++;

        return min(0.95, max(0.5, $matches / $total));
    }

    /**
     * Log AI classification to database.
     *
     * @param int $ticketId
     * @param string $prompt
     * @param array $response
     * @param int|null $processingTime
     * @param string $status
     * @param string|null $errorMessage
     * @return AiLog
     */
    public function logClassification(
        int $ticketId,
        string $prompt,
        array $response,
        ?int $processingTime = null,
        string $status = 'success',
        ?string $errorMessage = null
    ): AiLog {
        return AiLog::create([
            'ticket_id' => $ticketId,
            'model' => $response['model'] ?? config('ai.deepseek.model'),
            'prompt' => $prompt,
            'response' => $response,
            'confidence' => $response['confidence'] ?? null,
            'processing_time_ms' => $processingTime,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }
}
