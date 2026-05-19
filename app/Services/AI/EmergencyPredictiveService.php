<?php

namespace App\Services\AI;

use App\Models\User;

class EmergencyPredictiveService
{
    /**
     * Predict the likelihood of emergency escalation.
     */
    public function predictVulnerability(User $user): array
    {
        $vulnerability = 0;
        $factors = [];

        // 1. Historical SOS Frequency
        $sosCount = $user->emergencyRequests()->where('created_at', '>=', now()->subMonths(3))->count();
        if ($sosCount > 2) {
            $vulnerability += 40;
            $factors[] = "Multiple emergency triggers in the last 90 days.";
        }

        // 2. Trend Deterioration
        $latestSnapshot = $user->healthSnapshots()->latest()->first();
        if ($latestSnapshot && ($latestSnapshot->data['risks']['average'] ?? 0) > 70) {
            $vulnerability += 30;
            $factors[] = "High underlying chronic instability.";
        }

        return [
            'score' => min(100, $vulnerability),
            'factors' => $factors,
            'level' => $this->getVulnerabilityLevel($vulnerability)
        ];
    }

    protected function getVulnerabilityLevel(int $score): string
    {
        if ($score >= 70) return 'CRITICAL';
        if ($score >= 40) return 'ELEVATED';
        return 'STABLE';
    }
}
