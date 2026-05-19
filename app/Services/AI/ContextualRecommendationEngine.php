<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AIRecommendation;
use Illuminate\Support\Facades\Log;

class ContextualRecommendationEngine
{
    protected GroqClient $groq;
    protected LongitudinalIntelligenceEngine $longitudinal;
    protected HealthConfidenceEngine $confidence;
    protected ReasoningConsistencyService $consistency;

    public function __construct(
        GroqClient $groq,
        LongitudinalIntelligenceEngine $longitudinal,
        HealthConfidenceEngine $confidence,
        ReasoningConsistencyService $consistency
    ) {
        $this->groq = $groq;
        $this->longitudinal = $longitudinal;
        $this->confidence = $confidence;
        $this->consistency = $consistency;
    }

    /**
     * Generate a contextual recommendation suite for the entity (User or FamilyMember).
     */
    public function generate($entity): AIRecommendation
    {
        $user = ($entity instanceof User) ? $entity : $entity->user;
        $familyMemberId = ($entity instanceof \App\Models\FamilyMember) ? $entity->id : null;

        $confidenceScore = $this->confidence->calculateConfidence($user);
        $confidenceLang = $this->confidence->getConfidenceLanguage($confidenceScore);
        
        // Fetch health context
        $context = $this->gatherContext($entity);
        
        $prompt = $this->buildPrompt($entity, $context, $confidenceLang);
        
        try {
            $analysis = $this->groq->chat([
                ['role' => 'system', 'content' => $prompt['system']],
                ['role' => 'user', 'content' => $prompt['user']]
            ]);

            // Consistency Check
            $consistencyCheck = $this->consistency->validateConsistency($user, $analysis);
            $stabilityHash = $this->consistency->generateStabilityHash($analysis);

            return $entity->aiRecommendations()->create([
                'user_id' => $user->id,
                'family_member_id' => $familyMemberId,
                'content' => $analysis['summary'] ?? 'Health analysis complete.',
                'type' => 'insight',
                'confidence_score' => $confidenceScore,
                'confidence_language' => $confidenceLang,
                'model_used' => config('groq.model'),
                'rationale' => $analysis['rationale'] ?? null,
                'supporting_evidence' => $analysis['evidence'] ?? [],
                'stability_hash' => $stabilityHash,
                'parent_recommendation_id' => $consistencyCheck['parent_id'] ?? null,
                'reasoning_trace' => json_encode($consistencyCheck['issues'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('Recommendation Generation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function gatherContext($entity): array
    {
        $user = ($entity instanceof User) ? $entity : $entity->user;

        $vitals = $entity->healthIndicators()->latest()->take(10)->get();
        $trends = [
            'blood_pressure' => $this->longitudinal->analyze($entity, 'blood_pressure_sys'),
            'glucose' => $this->longitudinal->analyze($entity, 'blood_glucose_fasting'),
        ];

        return [
            'entity_name' => $entity->name,
            'entity_type' => ($entity instanceof User) ? 'Primary User' : 'Family Member (' . $entity->relation . ')',
            'vitals' => $vitals,
            'trends' => $trends,
            'profile' => $entity->medicalProfile,
            'medications' => $entity->medications()->where('is_active', true)->get()
        ];
    }

    protected function buildPrompt($entity, array $context, string $confidenceLang): array
    {
        return [
            'system' => "You are a senior clinical health analyst. 
            Philosophy: 
            1. Be calm, supportive, and non-alarmist.
            2. Prefer monitoring over aggressive warnings.
            3. Tone should be: {$confidenceLang}.
            
            Entity: {$context['entity_name']} ({$context['entity_type']})

            Output JSON with:
            - summary: A clear, human-centric recommendation.
            - rationale: Explain the 'Why' behind this.
            - evidence: Top 3 corroborating factors.
            - risks: {cardiac: int, diabetes: int, respiratory: int}",
            
            'user' => "Analyze the following health state for {$context['entity_name']}:\n" . json_encode($context)
        ];
    }
}
