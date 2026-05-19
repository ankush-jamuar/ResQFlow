<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AIRecommendation;
use Illuminate\Support\Facades\Log;

class ReasoningConsistencyService
{
    /**
     * Validate a new recommendation against historical context.
     */
    public function validateConsistency(User $user, array $newAnalysis): array
    {
        $lastRecommendation = $user->aiRecommendations()
            ->where('type', 'insight')
            ->latest()
            ->first();

        if (!$lastRecommendation) {
            return [
                'is_consistent' => true,
                'explanation' => 'Initial analysis established.'
            ];
        }

        $issues = [];
        
        // 1. Detect Risk Jumps
        $previousRisks = $user->healthSnapshots()->latest()->first()?->data['risks'] ?? [];
        $newRisks = $newAnalysis['risks'] ?? [];

        foreach (['cardiac', 'diabetes', 'respiratory'] as $riskType) {
            $prev = $previousRisks[$riskType] ?? 0;
            $new = $newRisks[$riskType] ?? 0;

            if (abs($new - $prev) > 30) {
                $issues[] = "Significant jump in {$riskType} risk from {$prev}% to {$new}% without sufficient explanation.";
            }
        }

        // 2. Check for contradictory advice (Simple keyword matching for now)
        $newContent = strtolower(json_encode($newAnalysis));
        $oldContent = strtolower($lastRecommendation->content);

        if (str_contains($oldContent, 'low risk') && str_contains($newContent, 'high risk')) {
            $issues[] = "Contradictory risk assessment detected: Previously 'Low', now 'High'.";
        }

        return [
            'is_consistent' => empty($issues),
            'issues' => $issues,
            'parent_id' => $lastRecommendation->id,
            'explanation' => empty($issues) ? 'Consistent with previous reasoning.' : 'Potential reasoning instability detected.'
        ];
    }

    /**
     * Generate a stability hash to track recommendation persistence.
     */
    public function generateStabilityHash(array $data): string
    {
        // Hash the core findings, ignoring minor fluctuations
        $core = [
            'risks' => array_map(fn($v) => round($v / 5) * 5, $data['risks'] ?? []), // Round to nearest 5%
            'recommendations' => array_slice($data['recommendations'] ?? [], 0, 3)
        ];
        
        return md5(json_encode($core));
    }
}
