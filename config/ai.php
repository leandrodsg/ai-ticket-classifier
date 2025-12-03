<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DeepSeek AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for DeepSeek AI integration used for automatic ticket
    | classification and sentiment analysis.
    |
    */

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'api_url' => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1'),
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'mock_mode' => env('DEEPSEEK_MOCK_MODE', false),

        /*
        |--------------------------------------------------------------------------
        | Request Configuration
        |--------------------------------------------------------------------------
        */
        'timeout' => env('DEEPSEEK_TIMEOUT', 30), // seconds
        'retries' => env('DEEPSEEK_RETRIES', 3),
        'retry_delay' => env('DEEPSEEK_RETRY_DELAY', 1000), // milliseconds

        /*
        |--------------------------------------------------------------------------
        | Classification Settings
        |--------------------------------------------------------------------------
        */
        'temperature' => env('DEEPSEEK_TEMPERATURE', 0.3), // Lower for more consistent results
        'max_tokens' => env('DEEPSEEK_MAX_TOKENS', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback AI Providers
    |--------------------------------------------------------------------------
    |
    | Alternative AI providers to use when the primary DeepSeek API fails.
    | Providers are tried in order until one succeeds.
    |
    */
    'fallback_providers' => [
        'grok' => [
            'enabled' => env('GROK_ENABLED', true),
            'api_key' => env('GROK_API_KEY'),
            'api_url' => env('GROK_API_URL', 'https://api.x.ai/v1/chat/completions'),
            'model' => env('GROK_MODEL', 'grok-beta'),
            'timeout' => env('GROK_TIMEOUT', 30),
            'temperature' => env('GROK_TEMPERATURE', 0.3),
            'max_tokens' => env('GROK_MAX_TOKENS', 500),
        ],
        'openrouter' => [
            'enabled' => env('OPENROUTER_ENABLED', false), // Disabled by default
            'api_key' => env('OPENROUTER_API_KEY'),
            'api_url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
            'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.2-3b-instruct:free'),
            'timeout' => env('OPENROUTER_TIMEOUT', 30),
            'temperature' => env('OPENROUTER_TEMPERATURE', 0.3),
            'max_tokens' => env('OPENROUTER_MAX_TOKENS', 500),
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
        | These are used for validation and fallback logic.
        |
        */
        'valid_categories' => ['technical', 'commercial', 'billing', 'general', 'support'],
        'valid_sentiments' => ['positive', 'negative', 'neutral'],

        /*
        |--------------------------------------------------------------------------
        | Confidence Thresholds
        |--------------------------------------------------------------------------
        |
        | Minimum confidence levels for accepting AI classifications.
        | Below these thresholds, classification may be flagged for review.
        |
        */
        'min_confidence' => env('AI_MIN_CONFIDENCE', 0.6),
        'high_confidence' => env('AI_HIGH_CONFIDENCE', 0.8),

        /*
        |--------------------------------------------------------------------------
        | Fallback Behavior
        |--------------------------------------------------------------------------
        */
        'always_use_mock' => env('AI_ALWAYS_USE_MOCK', false), // Force mock mode for all requests
        'log_failures' => env('AI_LOG_FAILURES', true), // Log API failures
    ],
];
