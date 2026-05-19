<?php

return [
    'api_key' => env('GROQ_API_KEY'),
    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1/'),
    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
    'vision_model' => env('GROQ_VISION_MODEL', 'llama-3.2-11b-vision-preview'),
    'timeout' => env('GROQ_TIMEOUT', 30),
    'max_retries' => 3,
];
