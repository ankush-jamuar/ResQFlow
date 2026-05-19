<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Medication;

class MedicationIntelligenceService
{
    /**
     * Detect conflicts and adherence risks in medication regimen.
     */
    public function analyzeRegimen(User $user): array
    {
        $medications = $user->medications()->where('is_active', true)->get();
        $insights = [];

        // 1. Detect Duplicate Therapies
        $names = $medications->pluck('name')->map(fn($n) => strtolower($n));
        $duplicates = $names->duplicates();
        if ($duplicates->isNotEmpty()) {
            foreach ($duplicates as $dup) {
                $insights[] = [
                    'type' => 'DUPLICATE_THERAPY',
                    'severity' => 'IMPORTANT',
                    'message' => "Potential duplicate therapy detected for '{$dup}'. Ensure you are not taking multiple versions of the same medication."
                ];
            }
        }

        // 2. Timing Conflicts (Placeholder for more complex logic)
        // In a real app, we'd check if meds interact or have clashing timings.

        return [
            'count' => $medications->count(),
            'insights' => $insights,
            'status' => empty($insights) ? 'optimized' : 'review_required'
        ];
    }
}
