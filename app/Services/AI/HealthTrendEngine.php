<?php

namespace App\Services\AI;

use App\Models\User;
use Carbon\Carbon;

class HealthTrendEngine
{
    /**
     * Compare historical snapshots to identify health trends.
     */
    public function getTrends(User $user): array
    {
        $snapshots = $user->healthSnapshots()->latest()->take(5)->get();
        
        if ($snapshots->count() < 2) {
            return [
                'status' => 'stable',
                'message' => 'Insufficient data for trend analysis.',
                'indicators' => []
            ];
        }

        $current = $snapshots->first();
        $previous = $snapshots->get(1);

        $trends = [];
        $worsening = 0;
        $improving = 0;

        foreach (['preparedness_score', 'cardiac_risk', 'diabetes_risk', 'respiratory_risk'] as $key) {
            $diff = $current->$key - $previous->$key;
            
            // For risk, lower is better. For preparedness, higher is better.
            $isPositive = ($key === 'preparedness_score') ? ($diff > 0) : ($diff < 0);
            $isStable = abs($diff) <= 2;

            $trends[$key] = [
                'current' => $current->$key,
                'previous' => $previous->$key,
                'direction' => $isStable ? 'stable' : ($isPositive ? 'up' : 'down'),
                'is_positive' => $isPositive
            ];

            if (!$isStable) {
                if ($isPositive) $improving++; else $worsening++;
            }
        }

        $overall = 'stable';
        if ($worsening > $improving) $overall = 'declining';
        if ($improving > $worsening) $overall = 'improving';

        return [
            'status' => $overall,
            'trends' => $trends,
            'last_sync' => $current->created_at->diffForHumans()
        ];
    }
}
