<?php

namespace App\Services\AI;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class HealthRiskEngine
{
    /**
     * Calculate comprehensive disease risks based on real patient data.
     */
    public function calculateRisks(User $user): array
    {
        $profile = $user->medicalProfile;
        if (!$profile) {
            return $this->getDefaultRisks();
        }

        $age = $profile->date_of_birth ? $profile->date_of_birth->age : 30;
        $bmi = $this->calculateBMI($profile->weight_kg, $profile->height_cm);
        $indicators = $user->healthIndicators()->latest()->get()->pluck('value', 'key');
        
        $risks = [
            'cardiac' => $this->scoreCardiacRisk($age, $bmi, $indicators, $profile->chronic_conditions),
            'diabetes' => $this->scoreDiabetesRisk($age, $bmi, $indicators, $profile->chronic_conditions),
            'respiratory' => $this->scoreRespiratoryRisk($age, $indicators, $profile->chronic_conditions),
            'preparedness' => $this->calculatePreparedness($user),
        ];

        // Average risk excluding preparedness
        $risks['average'] = round(($risks['cardiac'] + $risks['diabetes'] + $risks['respiratory']) / 3);

        return $risks;
    }

    protected function calculateBMI(?float $weight, ?float $height): float
    {
        if (!$weight || !$height) return 22.0;
        $heightM = $height / 100;
        return round($weight / ($heightM * $heightM), 1);
    }

    protected function scoreCardiacRisk(int $age, float $bmi, $indicators, ?string $conditions): int
    {
        $score = 10; // Base risk

        // Age factor
        if ($age > 50) $score += 15;
        if ($age > 70) $score += 20;

        // BMI factor
        if ($bmi > 25) $score += 10;
        if ($bmi > 30) $score += 15;

        // BP Factor (Systolic)
        $bp = (int) ($indicators['blood_pressure_sys'] ?? 120);
        if ($bp > 140) $score += 20;
        if ($bp > 160) $score += 30;

        // Condition factor
        if (str_contains(strtolower($conditions ?? ''), 'hypertension')) $score += 15;
        if (str_contains(strtolower($conditions ?? ''), 'heart')) $score += 25;

        return min(95, $score);
    }

    protected function scoreDiabetesRisk(int $age, float $bmi, $indicators, ?string $conditions): int
    {
        $score = 5;

        if ($bmi > 25) $score += 15;
        if ($bmi > 30) $score += 25;

        $glucose = (int) ($indicators['blood_glucose_fasting'] ?? 90);
        if ($glucose > 100) $score += 20;
        if ($glucose > 126) $score += 40;

        if (str_contains(strtolower($conditions ?? ''), 'diabetes')) $score += 50;

        return min(95, $score);
    }

    protected function scoreRespiratoryRisk(int $age, $indicators, ?string $conditions): int
    {
        $score = 10;

        $spo2 = (int) ($indicators['spo2'] ?? 98);
        if ($spo2 < 95) $score += 20;
        if ($spo2 < 90) $score += 40;

        if (str_contains(strtolower($conditions ?? ''), 'asthma')) $score += 20;
        if (str_contains(strtolower($conditions ?? ''), 'copd')) $score += 30;

        return min(95, $score);
    }

    protected function calculatePreparedness(User $user): int
    {
        $score = 40; // Base score for having an account

        if ($user->medicalProfile) $score += 20;
        if ($user->familyMembers()->exists()) $score += 10;
        if ($user->medications()->exists()) $score += 10;
        if ($user->medicalReports()->exists()) $score += 20;

        return min(100, $score);
    }

    protected function getDefaultRisks(): array
    {
        return [
            'cardiac' => 15,
            'diabetes' => 10,
            'respiratory' => 10,
            'preparedness' => 40,
            'average' => 12
        ];
    }
}
