<?php

namespace App\Data\AI;

class MedicationInsightData
{
    public function __construct(
        public readonly array $interactions,
        public readonly array $side_effects,
        public readonly array $effectiveness_notes,
        public readonly array $conflicts
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            interactions: $data['interactions'] ?? [],
            side_effects: $data['side_effects'] ?? [],
            effectiveness_notes: $data['effectiveness_notes'] ?? [],
            conflicts: $data['conflicts'] ?? []
        );
    }
}
