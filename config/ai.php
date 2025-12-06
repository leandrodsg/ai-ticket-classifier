<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenRouter AI Configuration (Primary and Only Provider)
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenRouter AI integration - our only AI provider.
    | OpenRouter provides free access to multiple AI models.
    |
    */

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'api_url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
        'timeout' => (int) env('OPENROUTER_TIMEOUT', 30),
        'temperature' => (float) env('OPENROUTER_TEMPERATURE', 0.3),
        'max_tokens' => (int) env('OPENROUTER_MAX_TOKENS', 500),

        /*
        |--------------------------------------------------------------------------
        | Model Fallback Chain
        |--------------------------------------------------------------------------
        |
        | Ordered list of free models to try in sequence.
        | If one fails (rate limit, unavailable), try the next.
        |
        */
        'models' => [
            'meta-llama/llama-3.3-70b-instruct:free',    // Primary - most capable
            'meta-llama/llama-3.2-3b-instruct:free',     // Backup 1
            'openai/gpt-oss-20b:free',                   // Backup 2
            'google/gemma-3n-e2b-it:free',               // Backup 3
            'mistralai/mistral-7b-instruct:free',        // Backup 4
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global AI Settings
    |--------------------------------------------------------------------------
    */
    'global' => [
        /*
        |--------------------------------------------------------------------------
        | Categories and Sentiments
        |--------------------------------------------------------------------------
        |
        | Valid categories and sentiments that the AI can classify tickets into.
        |
        */
        'valid_categories' => ['technical', 'commercial', 'billing', 'general', 'support'],
        'valid_sentiments' => ['positive', 'negative', 'neutral'],

        /*
        |--------------------------------------------------------------------------
        | Behavior
        |--------------------------------------------------------------------------
        */
        'always_use_mock' => env('AI_ALWAYS_USE_MOCK', false), // Force mock mode for all requests
        'log_failures' => env('AI_LOG_FAILURES', true), // Log API failures
    ],
];
