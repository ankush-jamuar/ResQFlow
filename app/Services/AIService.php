<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AIService
{
    /**
     * Parse raw text from OCR into structured medical data.
     */
    public function parseReport(string $text): array
    {
        Log::info('[AI] Starting report parsing pipeline...');

        // In a real implementation, we would call Gemini/OpenAI here.
        // For Phase 2 initialization, we use the high-fidelity DEMO MODE.
        
        return $this->getDemoParsingResults($text);
    }

    /**
     * Generate a health risk summary and recommendations.
     */
    public function analyzeHealthProfile(array $parsedData): array
    {
        Log::info('[AI] Generating health intelligence summary...');

        // Logic to generate risk levels, warnings, and suggestions.
        return [
            'risk_level' => $this->calculateRiskLevel($parsedData),
            'summary' => 'Patient shows signs of chronic hypertension with controlled medication. No immediate emergency risks detected.',
            'recommendations' => [
                'Schedule a cardiac follow-up within 3 months.',
                'Keep an updated log of daily blood pressure readings.',
                'Ensure high-protein, low-sodium diet adherence.'
            ],
            'warnings' => [
                'Allergy detected: Penicillin. Avoid all beta-lactam antibiotics.'
            ]
        ];
    }

    private function calculateRiskLevel(array $data): string
    {
        // Simple logic: if chronic conditions exist, risk is medium+
        if (!empty($data['chronic_conditions'])) return 'medium';
        return 'low';
    }

    private function getDemoParsingResults(string $text): array
    {
        // High-fidelity mock extraction simulating a real AI response
        return [
            'blood_group' => 'B+',
            'allergies' => ['Penicillin', 'Peanuts'],
            'chronic_conditions' => ['Hypertension', 'Type 2 Diabetes'],
            'current_medications' => ['Metformin 500mg', 'Amlodipine 5mg'],
            'vitals' => [
                'bp' => '140/90',
                'heart_rate' => '78 bpm',
                'spO2' => '98%'
            ],
            'confidence' => 0.94,
            'metadata' => [
                'source' => 'OCR Extraction',
                'engine' => 'ResQFlow-AI-Demo-v1'
            ]
        ];
    }
}
