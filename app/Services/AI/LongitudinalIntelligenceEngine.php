<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\HealthIndicator;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LongitudinalIntelligenceEngine
{
    /**
     * Analyze health indicators over time to establish baselines and detect patterns.
     */
    public function analyze($entity, string $key): array
    {
        $indicators = $entity->healthIndicators()
            ->where('key', $key)
            ->latest('measured_at')
            ->take(20)
            ->get()
            ->reverse();

        if ($indicators->count() < 3) {
            return [
                'status' => 'insufficient_data',
                'baseline' => null,
                'trend' => 'stable',
                'volatility' => 'low'
            ];
        }

        $values = $indicators->pluck('value')->map(fn($v) => (float)$v);
        $baseline = $this->calculateBaseline($values);
        $volatility = $this->calculateVolatility($values);
        $trend = $this->detectTrend($values);

        return [
            'status' => 'analyzed',
            'baseline' => $baseline,
            'current' => $values->last(),
            'deviation' => round($values->last() - $baseline, 2),
            'trend' => $trend,
            'volatility' => $volatility,
            'is_anomaly' => $this->checkAnomaly($values->last(), $baseline, $volatility),
            'data_points' => $indicators->count()
        ];
    }

    protected function calculateBaseline(Collection $values): float
    {
        // Simple moving average as a baseline
        return round($values->avg(), 2);
    }

    protected function calculateVolatility(Collection $values): string
    {
        $avg = $values->avg();
        $variance = $values->map(fn($v) => pow($v - $avg, 2))->avg();
        $stdDev = sqrt($variance);
        
        $coefficientOfVariation = ($avg != 0) ? ($stdDev / $avg) : 0;

        if ($coefficientOfVariation > 0.15) return 'high';
        if ($coefficientOfVariation > 0.05) return 'moderate';
        return 'low';
    }

    protected function detectTrend(Collection $values): string
    {
        $firstHalf = $values->take($values->count() / 2)->avg();
        $secondHalf = $values->slice($values->count() / 2)->avg();

        $diff = $secondHalf - $firstHalf;
        $threshold = $firstHalf * 0.05; // 5% change threshold

        if ($diff > $threshold) return 'upward';
        if ($diff < -$threshold) return 'downward';
        return 'stable';
    }

    protected function checkAnomaly(float $current, float $baseline, string $volatility): bool
    {
        $thresholdMultiplier = ($volatility === 'high') ? 2.0 : 1.5;
        $threshold = $baseline * 0.2 * $thresholdMultiplier; // 20% deviation threshold adjusted by volatility

        return abs($current - $baseline) > $threshold;
    }
}
