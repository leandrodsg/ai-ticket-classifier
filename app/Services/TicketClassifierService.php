<?php

namespace App\Services;

use App\Models\AiLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

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
        if (config('ai.global.always_use_mock')) {
            Log::info('Using mock classification (forced by config)');
            return $this->mockClassify($description);
        }

        try {
            Log::info('Using OpenRouter AI provider');
            $result = $this->classifyWithOpenRouter($description);
            Log::info('OpenRouter AI provider succeeded');
            return $result;
        } catch (\Exception $e) {
            Log::warning('OpenRouter AI provider failed, using mock classification', [
                'error' => $e->getMessage()
            ]);

            return $this->mockClassify($description);
        }
    }

    /**
     * Classify a ticket and calculate priority using ITIL matrix.
     *
     * @param string $description
     * @return array
     * @throws \Exception
     */
    public function classifyWithPriority(string $description): array
    {
        $classification = $this->classify($description);

        try {
            $priorityService = app(PriorityCalculatorService::class);
            $priorityData = $priorityService->calculateFromCategoryAndSentiment(
                $classification['category'],
                $classification['sentiment']
            );

            $classification = array_merge($classification, $priorityData);

            Log::info('Priority calculated successfully', [
                'category' => $classification['category'],
                'sentiment' => $classification['sentiment'],
                'priority' => $classification['priority'],
                'impact_level' => $classification['impact_level'],
                'urgency_level' => $classification['urgency_level']
            ]);

        } catch (\Exception $e) {
            Log::error('Priority calculation failed, continuing without priority', [
                'error' => $e->getMessage(),
                'category' => $classification['category'],
                'sentiment' => $classification['sentiment']
            ]);

            $classification['priority'] = null;
            $classification['impact_level'] = null;
            $classification['urgency_level'] = null;
        }

        return $classification;
    }

    /**
     * Classify using OpenRouter AI provider with model fallback.
     *
     * @param string $description
     * @return array
     * @throws \Exception
     */
    protected function classifyWithOpenRouter(string $description): array
    {
        $providerConfig = config('ai.openrouter');

        if (!$providerConfig) {
            throw new \Exception("OpenRouter not configured");
        }

        if (empty($providerConfig['api_key'])) {
            throw new \Exception("OpenRouter API key missing");
        }

        $models = $providerConfig['models'] ?? ['meta-llama/llama-3.2-3b-instruct:free'];

        foreach ($models as $model) {
            try {
                Log::info('Trying OpenRouter model', ['model' => $model]);
                $configWithModel = array_merge($providerConfig, ['model' => $model]);
                $result = $this->callAiProvider('openrouter', $configWithModel, $description);
                Log::info('OpenRouter model succeeded', ['model' => $model]);
                return $result;
            } catch (\Exception $e) {
                Log::warning('OpenRouter model failed, trying next', [
                    'model' => $model,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        throw new \Exception("All OpenRouter models failed");
    }

    /**
     * Call a specific AI provider API.
     *
     * @param string $providerName
     * @param array $providerConfig
     * @param string $description
     * @return array
     * @throws \Exception
     */
    protected function callAiProvider(string $providerName, array $providerConfig, string $description): array
    {
        Log::info("Starting AI provider call", ['provider' => $providerName, 'model' => $providerConfig['model']]);

        // Rate limiting: max 10 requests per minute per provider
        $rateLimiterKey = "ai_{$providerName}_requests";
        if (RateLimiter::tooManyAttempts($rateLimiterKey, 10)) {
            $availableIn = RateLimiter::availableIn($rateLimiterKey);
            Log::warning("Rate limit exceeded", ['provider' => $providerName, 'available_in' => $availableIn]);
            throw new \Exception("Rate limit exceeded for {$providerName}. Try again in {$availableIn} seconds.");
        }

        RateLimiter::hit($rateLimiterKey, 60); // 60 seconds window
        Log::info("Rate limiter hit", ['key' => $rateLimiterKey]);

        $startTime = microtime(true);

        try {
            $payload = [
                'model' => $providerConfig['model'],
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
                'temperature' => (float) ($providerConfig['temperature'] ?? 0.3),
                'max_tokens' => (int) ($providerConfig['max_tokens'] ?? 500),
            ];



            $response = Http::timeout((int) ($providerConfig['timeout'] ?? 30))
                ->retry(3, 1000, function ($exception) {
                    return $exception instanceof ConnectionException ||
                           $exception instanceof RequestException;
                })
                ->withToken($providerConfig['api_key'])
                ->post($providerConfig['api_url'], $payload);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return $this->parseApiResponse($response->json(), $processingTime, $providerName);
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            
            Log::error("{$providerName} API request failed", [
                'status' => $response->status(),
                'error_message' => $errorMessage,
                'model' => $providerConfig['model'],
            ]);

            throw new \Exception("{$providerName} API failed: {$response->status()} - {$errorMessage}");

        } catch (\Exception $e) {
            // Log error without sensitive data
            Log::error("{$providerName} API classification failed", [
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'description_length' => strlen($description),
                'processing_time_ms' => (int) ((microtime(true) - $startTime) * 1000),
                'model' => $providerConfig['model'] ?? 'unknown'
            ]);

            throw $e;
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

        $category = $this->detectCategory($lower);
        $sentiment = $this->detectSentiment($lower);
        $confidence = $this->calculateConfidence($lower);

        $priorityData = [];
        try {
            $priorityService = app(PriorityCalculatorService::class);
            $priorityData = $priorityService->calculateFromCategoryAndSentiment($category, $sentiment);
        } catch (\Exception $e) {
            Log::warning('Mock priority calculation failed', ['error' => $e->getMessage()]);
            $priorityData = [
                'priority' => null,
                'impact_level' => null,
                'urgency_level' => null,
            ];
        }

        return array_merge([
            'category' => $category,
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'reasoning' => "Mock classification based on keyword analysis",
            'model' => 'mock-classifier',
        ], $priorityData);
    }

    /**
     * Get the system prompt for AI classification.
     *
     * @return string
     */
    protected function getSystemPrompt(): string
    {
        $categories = implode(', ', config('ai.global.valid_categories'));
        $sentiments = implode(', ', config('ai.global.valid_sentiments'));

        return "You are an AI assistant that classifies customer support tickets. " .
               "IMPORTANT: Respond ONLY with valid JSON. No markdown, no code blocks, no explanations outside JSON. " .
               "Analyze the ticket description and return a JSON response with: " .
               "category ({$categories}), sentiment ({$sentiments}), confidence (0.0-1.0), " .
               "and reasoning (in English). Be precise and consistent.";
    }

    /**
     * Parse the API response from any AI provider.
     *
     * @param array $response
     * @param int $processingTime
     * @param string $providerName
     * @return array
     */
    protected function parseApiResponse(array $response, int $processingTime, string $providerName = 'deepseek'): array
    {
        // Extract the content from the response
        $content = $response['choices'][0]['message']['content'] ?? '';

        // Clean the content - remove markdown code blocks if present
        $content = trim($content);
        if (str_starts_with($content, '```json')) {
            $content = substr($content, 7); // Remove ```json
        }
        if (str_ends_with($content, '```')) {
            $content = substr($content, 0, -3); // Remove ```
        }
        $content = trim($content);

        // Try to parse JSON from the content
        $parsed = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response from {$providerName}: {$content}");
        }

        // Validate the response structure
        $this->validateClassificationResponse($parsed);

        return array_merge($parsed, [
            'processing_time_ms' => $processingTime,
            'model' => $response['model'] ?? config("ai.{$providerName}.model", 'unknown'),
            'provider' => $providerName,
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

        if (!in_array($response['category'], config('ai.global.valid_categories'))) {
            throw new \Exception("Invalid category: {$response['category']}");
        }

        if (!in_array($response['sentiment'], config('ai.global.valid_sentiments'))) {
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
        if (preg_match('/\b(bug|error|crash|fail|broken|not working|doesn\'?t work|system|software|application|database|server|login|export|import|upload|download)\b/i', $text)) {
            return 'technical';
        }

        if (preg_match('/\b(price|cost|buy|purchase|plan|subscription|quote|pricing|enterprise|pro|premium)\b/i', $text)) {
            return 'commercial';
        }

        if (preg_match('/\b(bill|invoice|payment|charge|refund|money|billing|account|subscription|cancel|renew)\b/i', $text)) {
            return 'billing';
        }

        if (preg_match('/\b(help|support|question|how|what|when|where|why|assistance|guide|tutorial|manual)\b/i', $text)) {
            return 'general';
        }

        return 'support';
    }

    protected function detectSentiment(string $text): string
    {
        if (preg_match('/\b(urgent|problem|issue|frustrated|angry|disappointed|terrible|awful)\b/i', $text)) {
            return 'negative';
        }

        if (preg_match('/\b(thank|great|excellent|amazing|happy|pleased|awesome|love)\b/i', $text)) {
            return 'positive';
        }

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

        $total += 4;
        if (preg_match('/\b(bug|error|crash|fail|broken|not working)\b/i', $text)) $matches++;
        if (preg_match('/\b(price|cost|buy|purchase|plan)\b/i', $text)) $matches++;
        if (preg_match('/\b(bill|invoice|payment|charge)\b/i', $text)) $matches++;
        if (preg_match('/\b(help|support|question|how|what)\b/i', $text)) $matches++;

        $total += 3;
        if (preg_match('/\b(urgent|problem|issue|frustrated|angry)\b/i', $text)) $matches++;
        if (preg_match('/\b(thank|great|excellent|amazing|happy)\b/i', $text)) $matches++;
        if (preg_match('/\b(please|could|would|can)\b/i', $text)) $matches++;

        return min(0.95, max(0.5, $matches / $total));
    }

    /**
     * Log AI classification to database (sanitized for privacy).
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
        // Sanitize sensitive data for privacy
        $sanitizedResponse = $response;
        unset($sanitizedResponse['reasoning']); // Remove potentially sensitive reasoning

        // Hash the prompt for privacy (keep length for analysis)
        $promptHash = hash('sha256', $prompt);
        $promptLength = strlen($prompt);

        return AiLog::create([
            'ticket_id' => $ticketId,
            'model' => $response['model'] ?? config('ai.openrouter.model'),
            'prompt' => "hashed:{$promptHash}:{$promptLength}", // Store hash instead of raw text
            'response' => $sanitizedResponse,
            'confidence' => $response['confidence'] ?? null,
            'processing_time_ms' => $processingTime,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }
}
