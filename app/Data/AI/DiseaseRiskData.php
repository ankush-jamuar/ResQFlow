<?php

namespace App\Data\AI;

class DiseaseRiskData
{
    public function __construct(
        public readonly array $cardiac_risk,
        public readonly array $diabetes_risk,
        public readonly array $stroke_risk,
        public readonly int $emergency_vulnerability_score,
        public readonly array $recommendations
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            cardiac_risk: $data['cardiac_risk'] ?? ['level' => 'low', 'percentage' => 0],
            diabetes_risk: $data['diabetes_risk'] ?? ['level' => 'low', 'percentage' => 0],
            stroke_risk: $data['stroke_risk'] ?? ['level' => 'low', 'percentage' => 0],
            emergency_vulnerability_score: (int)($data['emergency_vulnerability_score'] ?? 0),
            recommendations: $data['recommendations'] ?? []
        );
    }
}
