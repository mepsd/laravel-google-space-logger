<?php

return [
    'url' => env('GOOGLE_CHAT_WEBHOOK_URL'),
    'level' => env('GOOGLE_CHAT_LOG_LEVEL', 'debug'),

    // Thread key for grouping messages
    'thread_key' => env('GOOGLE_CHAT_THREAD_KEY'),

    // Space name for better organization
    'space_name' => env('GOOGLE_CHAT_SPACE_NAME'),

    // Include additional data
    'includeStackTrace' => true,
    'includeSqlQueries' => true,
    'includeRequestData' => true,

    // Maximum depth for context arrays/objects
    'maxContextDepth' => 3,

    // HTTP client configuration
    'timeout' => 5,
    'retries' => 3,
    'backoff' => true,
];
