<?php

return [
    'provider' => env('AI_PROVIDER', 'openai'),

    'claude' => [
        'api_key'    => env('ANTHROPIC_API_KEY'),
        'model'      => env('CLAUDE_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => (int) env('CLAUDE_MAX_TOKENS', 4096),
    ],
];