<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class MedicalSafetyService
{
    protected array $prohibitedKeywords = [
        'prescribe', 'take this', 'stop taking', 'diagnosis certainty',
        'cured', 'guaranteed', 'buy this', 'prescription'
    ];

    protected array $alarmistKeywords = [
        'critical danger', 'immediate death', 'fatal', 'dying', 'extreme emergency'
    ];

    /**
     * Validate AI output for medical safety and tone.
     */
    public function validate(string $content, float $confidence = 1.0): string
    {
        // 1. Detect dangerous advice
        foreach ($this->prohibitedKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                Log::warning('Dangerous AI content detected and blocked', ['keyword' => $keyword]);
                return $this->getSafetyFallback();
            }
        }

        // 2. Tone Control & False Positive Suppression
        if ($confidence < 0.5) {
            foreach ($this->alarmistKeywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    $content = str_ireplace($keyword, 'potential concern', $content);
                }
            }
        }

        // 3. Ensure disclaimer is present
        return $this->injectDisclaimer($content);
    }

    /**
     * Sanitize structured data before mapping to DTO.
     */
    public function sanitizeData(array $data): array
    {
        // Recursively clean arrays of any dangerous strings
        array_walk_recursive($data, function (&$item) {
            if (is_string($item)) {
                $item = str_ireplace($this->prohibitedKeywords, '[REDACTED FOR SAFETY]', $item);
            }
        });

        return $data;
    }

    protected function injectDisclaimer(string $content): string
    {
        $disclaimer = "\n\n> [!CAUTION]\n> AI-generated longitudinal guidance. Not a substitute for professional clinical consultation.";
        
        if (strpos($content, 'clinical consultation') === false) {
            return $content . $disclaimer;
        }

        return $content;
    }

    protected function getSafetyFallback(): string
    {
        return "The AI assistant detected a potential safety violation. Please consult a licensed medical professional for specific advice.";
    }
}
