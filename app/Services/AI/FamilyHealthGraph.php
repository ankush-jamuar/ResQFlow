<?php

namespace App\Services\AI;

use App\Models\User;

class FamilyHealthGraph
{
    /**
     * Identify hereditary risks based on family ecosystem data.
     */
    public function getInheritedRisks(User $user): array
    {
        $family = $user->familyMembers;
        $inherited = [];
        $conditionCounts = [];

        foreach ($family as $member) {
            $conditions = array_map('trim', explode(',', strtolower($member->chronic_conditions ?? '')));
            
            foreach ($conditions as $condition) {
                if (empty($condition)) continue;
                $conditionCounts[$condition] = ($conditionCounts[$condition] ?? 0) + 1;
            }
        }

        // Detect Clusters (Hereditary Patterns)
        foreach ($conditionCounts as $condition => $count) {
            $severity = $count > 1 ? 'High (Cluster Detected)' : 'Moderate';
            
            if (str_contains($condition, 'hypertension') || str_contains($condition, 'heart')) {
                $inherited['cardiac'] = [
                    'count' => $count,
                    'severity' => $severity,
                    'note' => 'Hereditary cardiac pattern identified in household.'
                ];
            }

            if (str_contains($condition, 'diabetes')) {
                $inherited['diabetes'] = [
                    'count' => $count,
                    'severity' => $severity,
                    'note' => 'Multiple diabetic lineage indicators detected.'
                ];
            }
        }

        return $inherited;
    }
}
