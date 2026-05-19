<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\MedicalReport;
use App\Data\AI\HealthSummaryData;
use Illuminate\Support\Facades\Log;

class MedicalAnalysisService
{
    protected GroqClient $groq;
    protected OCRExtractionService $ocr;
    protected MedicalSafetyService $safety;
    protected HealthMemoryEngine $memory;
    protected ContextualRecommendationEngine $recommendationEngine;
    protected HealthConfidenceEngine $confidence;

    public function __construct(
        GroqClient $groq,
        OCRExtractionService $ocr,
        MedicalSafetyService $safety,
        HealthMemoryEngine $memory,
        ContextualRecommendationEngine $recommendationEngine,
        HealthConfidenceEngine $confidence
    ) {
        $this->groq = $groq;
        $this->ocr = $ocr;
        $this->safety = $safety;
        $this->memory = $memory;
        $this->recommendationEngine = $recommendationEngine;
        $this->confidence = $confidence;
    }

    /**
     * Run full intelligence extraction from a report.
     */
    public function analyzeReport(MedicalReport $report, string $extractedText): array
    {
        $promptConfig = require app_path('Services/AI/Prompts/analyze_report.prompt.php');
        
        $messages = [
            ['role' => 'system', 'content' => $promptConfig['system']],
            ['role' => 'user', 'content' => str_replace('{REPORT_TEXT}', $extractedText, $promptConfig['user'])]
        ];

        try {
            $rawAnalysis = $this->groq->chat($messages);
            
            // Validate and sanitize via Safety Layer
            $safeAnalysis = $this->safety->sanitizeData($rawAnalysis);
            
            // Target the correct entity (Primary User or Family Member)
            $target = $report->familyMember ?: $report->user;
            
            // Map to structured entities
            $this->storeExtractedData($target, $safeAnalysis, $report->id);

            // AUTO-SYNC TO PROFILE
            $this->syncToProfile($target, $safeAnalysis, $report);

            // Update report status
            $report->update([
                'status' => 'completed',
                'processed_at' => now(),
                'parsed_data' => $safeAnalysis
            ]);

            return $safeAnalysis;

        } catch (\Exception $e) {
            Log::error('Medical Analysis Failed: ' . $e->getMessage());
            $report->update(['status' => 'failed', 'error_reason' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function storeExtractedData($entity, array $data, int $reportId): void
    {
        $user = ($entity instanceof User) ? $entity : $entity->user;
        $familyMemberId = ($entity instanceof \App\Models\FamilyMember) ? $entity->id : null;

        // 1. Store Indicators
        $confidenceScore = $this->confidence->calculateConfidence($user);
        foreach ($data['vitals'] ?? [] as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $entity->healthIndicators()->create([
                        'user_id' => $user->id,
                        'family_member_id' => $familyMemberId,
                        'key' => $key . '_' . $subKey,
                        'value' => (string) $subValue,
                        'unit' => null,
                        'measured_at' => now(),
                        'confidence_score' => $confidenceScore,
                        'source_type' => 'ai_extraction'
                    ]);
                }
                continue;
            }

            $entity->healthIndicators()->create([
                'user_id' => $user->id,
                'family_member_id' => $familyMemberId,
                'key' => (string) $key,
                'value' => (string) $value,
                'unit' => null,
                'measured_at' => now(),
                'confidence_score' => $confidenceScore,
                'source_type' => 'ai_extraction'
            ]);
        }

        // 2. Store Medications
        foreach ($data['medications'] ?? [] as $med) {
            if (!is_array($med) || empty($med['name'])) continue;

            $entity->medications()->updateOrCreate(
                [
                    'name' => $med['name'],
                    'user_id' => $user->id,
                    'family_member_id' => $familyMemberId
                ],
                [
                    'dosage' => $med['dosage'] ?? null,
                    'purpose' => $med['purpose'] ?? null,
                    'is_active' => true
                ]
            );
        }

        // 3. Generate Recommendations
        $this->recommendationEngine->generate($user); // Recommendation engine might need update for family members

        // 4. Invalidate health memory cache
        $this->memory->invalidateContext($user);
    }

    /**
     * Synchronize extracted intelligence with the core Medical Profile.
     */
    protected function syncToProfile($entity, array $data, MedicalReport $report): void
    {
        $profile = $entity->medicalProfile ?: $entity->medicalProfile()->create([
            'user_id' => ($entity instanceof User) ? $entity->id : $entity->user_id,
            'family_member_id' => ($entity instanceof \App\Models\FamilyMember) ? $entity->id : null,
        ]);

        // Don't overwrite manually verified data
        if ($profile->is_manual_verified) {
            Log::info("Skipping AI profile sync for {$entity->name} - Profile is manually verified.");
            return;
        }

        $updates = [];
        $confidenceThreshold = 0.85;

        // Sync Blood Group
        if (!empty($data['blood_group']) && empty($profile->blood_group)) {
            $updates['blood_group'] = $data['blood_group'];
        }

        // Sync Allergies (Merge if needed, but here we replace if significantly more data)
        if (!empty($data['allergies']) && is_array($data['allergies'])) {
            $updates['allergies'] = implode(', ', $data['allergies']);
        }

        // Sync Chronic Conditions
        if (!empty($data['conditions']) && is_array($data['conditions'])) {
            $updates['chronic_conditions'] = implode(', ', $data['conditions']);
        }

        // Sync Weight/Height
        if (isset($data['vitals']['weight'])) {
            $updates['weight_kg'] = floatval($data['vitals']['weight']);
        }
        if (isset($data['vitals']['height'])) {
            $updates['height_cm'] = floatval($data['vitals']['height']);
        }

        if (!empty($updates)) {
            $updates['last_ai_sync_at'] = now();
            $updates['sync_source'] = "Report: {$report->file_name} (ID: {$report->id})";
            $profile->update($updates);
            
            Log::info("AI Profile Sync complete for {$entity->name}", ['fields' => array_keys($updates)]);
        }
    }
}
