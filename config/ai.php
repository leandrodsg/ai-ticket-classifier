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
        'min_confidence' => env('DEEPSEEK_MIN_CONFIDENCE', 0.6),
        'high_confidence' => env('DEEPSEEK_HIGH_CONFIDENCE', 0.8),
    ],
];
