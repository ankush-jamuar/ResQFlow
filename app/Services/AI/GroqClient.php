<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GroqClient
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key');
        $this->baseUrl = config('groq.base_url');
        $this->model = config('groq.model');
    }

    /**
     * Complete a chat request with structured JSON enforcement.
     */
    public function chat(array $messages, bool $jsonMode = true, ?string $model = null): array
    {
        $cacheKey = 'groq_completion_' . md5(serialize($messages) . $jsonMode . ($model ?? $this->model));
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(config('groq.timeout'))
                ->post($this->baseUrl . 'chat/completions', [
                    'model' => $model ?? $this->model,
                    'messages' => $messages,
                    'response_format' => $jsonMode ? ['type' => 'json_object'] : null,
                    'temperature' => 0.1,
                ]);

            if ($response->failed()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('AI Intelligence currently unavailable.');
            }

            $data = $response->json();
            $result = $data['choices'][0]['message']['content'] ?? '';

            if ($jsonMode) {
                $decoded = json_decode($result, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Malformed AI response.');
                }
                
                // Cache successful JSON responses for 1 hour to optimize tokens
                Cache::put($cacheKey, $decoded, 3600);
                return $decoded;
            }

            return ['content' => $result];

        } catch (\Exception $e) {
            Log::error('Groq Client Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Log token usage for cost monitoring.
     */
    protected function logUsage(array $data): void
    {
        Log::channel('ai_usage')->info('Groq Usage', [
            'model' => $data['model'] ?? 'unknown',
            'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $data['usage']['total_tokens'] ?? 0,
        ]);
    }
}
