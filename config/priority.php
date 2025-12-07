<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ITIL Priority System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the ITIL-based ticket prioritization system.
    | Defines the Impact × Urgency matrix and SLA definitions.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Category to Impact Mapping
    |--------------------------------------------------------------------------
    |
    | Maps ticket categories to ITIL impact levels.
    | Impact represents the magnitude of the business impact.
    |
    */
    'category_to_impact' => [
        'technical' => 'critical',    // System down, critical functionality broken
        'billing' => 'high',          // Financial impact, payment issues
        'commercial' => 'medium',     // Business operations affected
        'general' => 'low',           // General inquiries, minor issues
        'support' => 'low',           // Support requests, documentation
    ],

    /*
    |--------------------------------------------------------------------------
    | Sentiment to Urgency Mapping
    |--------------------------------------------------------------------------
    |
    | Maps ticket sentiments to ITIL urgency levels.
    | Urgency represents how quickly the issue needs resolution.
    |
    */
    'sentiment_to_urgency' => [
        'negative' => 'high',         // Angry/frustrated customers, urgent issues
        'neutral' => 'medium',        // Standard requests, normal priority
        'positive' => 'low',          // Positive feedback, non-urgent requests
    ],

    /*
    |--------------------------------------------------------------------------
    | ITIL Priority Matrix
    |--------------------------------------------------------------------------
    |
    | Defines the priority levels based on Impact × Urgency combinations.
    | Priority determines SLA and response times.
    |
    */
    'matrix' => [
        'critical' => [
            'high' => 'critical',     // Critical impact + High urgency = Critical priority
            'medium' => 'critical',   // Critical impact + Medium urgency = Critical priority
            'low' => 'high',          // Critical impact + Low urgency = High priority
        ],
        'high' => [
            'high' => 'critical',     // High impact + High urgency = Critical priority
            'medium' => 'high',       // High impact + Medium urgency = High priority
            'low' => 'medium',        // High impact + Low urgency = Medium priority
        ],
        'medium' => [
            'high' => 'high',         // Medium impact + High urgency = High priority
            'medium' => 'medium',     // Medium impact + Medium urgency = Medium priority
            'low' => 'low',           // Medium impact + Low urgency = Low priority
        ],
        'low' => [
            'high' => 'medium',       // Low impact + High urgency = Medium priority
            'medium' => 'low',        // Low impact + Medium urgency = Low priority
            'low' => 'low',           // Low impact + Low urgency = Low priority
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SLA Definitions (in hours)
    |--------------------------------------------------------------------------
    |
    | Service Level Agreements for each priority level.
    | Defines the maximum time allowed for resolution.
    |
    */
    'slas' => [
        'critical' => 1,      // 1 hour for critical priority
        'high' => 4,          // 4 hours for high priority
        'medium' => 24,       // 24 hours for medium priority
        'low' => 48,          // 48 hours for low priority
    ],

    /*
    |--------------------------------------------------------------------------
    | Valid Priority Levels
    |--------------------------------------------------------------------------
    |
    | All valid priority levels in the system.
    |
    */
    'valid_priorities' => ['critical', 'high', 'medium', 'low'],

    /*
    |--------------------------------------------------------------------------
    | Valid Impact Levels
    |--------------------------------------------------------------------------
    |
    | All valid impact levels in the system.
    |
    */
    'valid_impacts' => ['critical', 'high', 'medium', 'low'],

    /*
    |--------------------------------------------------------------------------
    | Valid Urgency Levels
    |--------------------------------------------------------------------------
    |
    | All valid urgency levels in the system.
    |
    */
    'valid_urgencies' => ['high', 'medium', 'low'],
];
