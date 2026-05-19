<?php

namespace App\Services\AI;

use App\Models\User;

class RiskGovernanceEngine
{
    protected const THRESHOLD_LOW = 30;
    protected const THRESHOLD_MEDIUM = 60;
    protected const THRESHOLD_HIGH = 85;

    /**
     * Governs the escalation of risk levels based on evidence depth.
     */
    public function govern(User $user, array $calculatedRisks, array $evidence): array
    {
        $governedRisks = [];
        $justifications = [];

        foreach ($calculatedRisks as $type => $score) {
            if ($type === 'average') continue;

            $previousScore = $user->healthSnapshots()->latest()->first()?->data['risks'][$type] ?? 0;
            $escalation = $score - $previousScore;

            // Rule: Jump to HIGH requires multi-factor evidence
            if ($score >= self::THRESHOLD_HIGH && count($evidence[$type] ?? []) < 3) {
                $score = min($score, self::THRESHOLD_HIGH - 1);
                $justifications[$type] = "Risk capped at 'Medium' due to insufficient corroborating evidence for 'High' classification.";
            }

            // Rule: Sudden jump > 40% requires verification
            if ($escalation > 40 && count($evidence[$type] ?? []) < 2) {
                $score = $previousScore + 20; // Smooth the jump
                $justifications[$type] = "Sudden risk spike detected. Smoothed for stability pending further data points.";
            }

            $governedRisks[$type] = $score;
        }

        $governedRisks['average'] = round(array_sum($governedRisks) / count($governedRisks));

        return [
            'risks' => $governedRisks,
            'justifications' => $justifications,
            'governance_level' => 'strict'
        ];
    }
}
