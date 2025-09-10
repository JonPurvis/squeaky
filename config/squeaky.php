<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Words Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration allows you to specify custom words that should be
    | treated as profanity or explicitly allowed in your application.
    |
    | The 'blocked_words' array contains words that will be treated as
    | profanity regardless of the locale-specific profanity lists.
    |
    | The 'allowed_words' array contains words that will be explicitly
    | allowed even if they appear in the locale-specific profanity lists.
    |
    */

    'blocked_words' => [
        // Add custom words specific to your application that should be blocked
        // Example: 'company_secret', 'internal_term', 'restricted_word'
    ],

    'allowed_words' => [
        // Add custom words specific to your application that should be allowed
        // Example: 'analytics', 'scunthorpe', 'penistone'
    ],

    /*
    |--------------------------------------------------------------------------
    | Case Sensitivity
    |--------------------------------------------------------------------------
    |
    | Determines whether word matching should be case-sensitive.
    | Set to false for case-insensitive matching (recommended).
    |
    */
    'case_sensitive' => false,
];
