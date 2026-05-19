<?php

namespace App\Data\AI;

class HealthSummaryData
{
    public function __construct(
        public readonly string $overview,
        public readonly array $risks,
        public readonly array $warnings,
        public readonly array $vulnerabilities,
        public readonly float $confidence_score,
        public readonly string $reasoning
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            overview: $data['overview'] ?? '',
            risks: $data['risks'] ?? [],
            warnings: $data['warnings'] ?? [],
            vulnerabilities: $data['vulnerabilities'] ?? [],
            confidence_score: (float)($data['confidence_score'] ?? 0),
            reasoning: $data['reasoning'] ?? ''
        );
    }
}
