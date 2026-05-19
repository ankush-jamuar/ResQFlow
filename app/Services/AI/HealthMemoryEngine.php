<?php

namespace App\Services\AI;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HealthMemoryEngine
{
    /**
     * Generate a compressed health context for the AI.
     * Priority: Indicators > Medications > Chronic Conditions > Recent Summaries.
     */
    public function getCompressedContext(User $user): string
    {
        $cacheKey = "health_context_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $user->load(['medicalProfile', 'medications', 'healthIndicators', 'healthSnapshots']);

            $context = "PATIENT PROFILE:\n";
            $context .= "Name: {$user->name}\n";
            
            if ($user->medicalProfile) {
                $context .= "Blood Group: {$user->medicalProfile->blood_group}\n";
                $context .= "Chronic Conditions: {$user->medicalProfile->chronic_conditions}\n";
                $context .= "Known Allergies: {$user->medicalProfile->allergies}\n";
            }

            $context .= "\nCURRENT MEDICATIONS:\n";
            foreach ($user->medications()->where('is_active', true)->get() as $med) {
                $context .= "- {$med->name} ({$med->dosage}, {$med->timing})\n";
            }

            $context .= "\nRECENT VITALS/INDICATORS:\n";
            foreach ($user->healthIndicators()->latest('measured_at')->take(10)->get() as $indicator) {
                $context .= "- {$indicator->key}: {$indicator->value}{$indicator->unit} ({$indicator->measured_at->format('Y-m-d')})\n";
            }

            $context .= "\nPREVIOUS INTELLIGENCE SUMMARY:\n";
            $latestSummary = $user->healthSnapshots()->where('type', 'summary')->latest()->first();
            if ($latestSummary) {
                $context .= $latestSummary->data['overview'] ?? 'No recent summary available.';
            }

            return $context;
        });
    }

    /**
     * Clear the health context cache (to be called after updates).
     */
    public function invalidateContext(User $user): void
    {
        Cache::forget("health_context_{$user->id}");
    }
}
