<?php

namespace App\Services\AI;

use App\Models\User;
use Carbon\Carbon;

class HealthConfidenceEngine
{
    /**
     * Calculate health profile confidence score (0.0 to 1.0).
     */
    public function calculateConfidence(User $user): float
    {
        $score = 0.0;
        $profile = $user->medicalProfile;

        // 1. Profile Completeness (40%)
        if ($profile) {
            if ($profile->date_of_birth) $score += 0.1;
            if ($profile->blood_type) $score += 0.05;
            if ($profile->weight_kg && $profile->height_cm) $score += 0.1;
            if ($profile->chronic_conditions) $score += 0.15;
        }

        // 2. Data Recency (30%)
        $latestIndicator = $user->healthIndicators()->latest('measured_at')->first();
        if ($latestIndicator) {
            $daysOld = $latestIndicator->measured_at->diffInDays(now());
            if ($daysOld < 7) $score += 0.3;
            elseif ($daysOld < 30) $score += 0.15;
            elseif ($daysOld < 90) $score += 0.05;
        }

        // 3. Evidence Consistency (30%)
        $reportCount = $user->medicalReports()->count();
        if ($reportCount >= 3) $score += 0.3;
        elseif ($reportCount >= 1) $score += 0.15;

        return min(1.0, $score);
    }

    /**
     * Determine the appropriate language based on confidence score.
     */
    public function getConfidenceLanguage(float $score): string
    {
        if ($score >= 0.8) return 'consistent_trend_detected';
        if ($score >= 0.5) return 'moderate_evidence_available';
        return 'limited_evidence_provisional';
    }

    /**
     * Check if a pattern is persistent enough to be considered "Chronic".
     */
    public function isPersistent(User $user, string $key, float $threshold, int $days = 30): bool
    {
        $indicators = $user->healthIndicators()
            ->where('key', $key)
            ->where('measured_at', '>=', now()->subDays($days))
            ->get();

        if ($indicators->count() < 3) return false;

        $highReadings = $indicators->filter(fn($i) => (float)$i->value >= $threshold);
        
        // If more than 70% of readings are above threshold, it's persistent
        return ($highReadings->count() / $indicators->count()) >= 0.7;
    }
}
