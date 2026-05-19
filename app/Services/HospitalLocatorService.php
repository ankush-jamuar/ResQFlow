<?php

namespace App\Services;

use App\Models\Ambulance;
use App\Models\Hospital;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HospitalLocatorService
{
    /**
     * Return the best hospital for an emergency:
     * - Never suspended hospitals
     * - Within configured max radius (or any distance if none set)
     * - Prefers hospitals that have available ambulances (AI routing)
     * - Falls back to pure nearest if ai_routing_enabled is false
     */
    public function nearest(float $lat, float $lng, array $excludeIds = []): ?Hospital
    {
        $maxRadius      = (int) SystemSetting::get('max_dispatch_radius_km', 200);
        $aiRouting      = (bool) SystemSetting::get('ai_routing_enabled', true);
        $highPriority   = (bool) SystemSetting::get('high_priority_intercept', false);

        // Base query: compute Haversine distance, exclude suspended and already-rejected
        $query = Hospital::selectRaw("
            hospitals.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance,
            (SELECT COUNT(*) FROM ambulances
             WHERE ambulances.hospital_id = hospitals.id
             AND ambulances.status = 'available') AS available_ambulances
        ", [$lat, $lng, $lat])
        ->where('is_suspended', false);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        // Filter by max radius
        if ($maxRadius > 0) {
            $query->havingRaw('distance <= ?', [$maxRadius]);
        }

        if ($aiRouting) {
            // AI routing: prioritized nearest hospital as primary sort, then resource availability
            $results = $query->orderByRaw('distance ASC, available_ambulances DESC, available_beds DESC')->get();

            // Pass 1: hospital WITH available ambulances AND available beds
            $ideal = $results->filter(fn($h) => $h->available_ambulances > 0 && $h->available_beds > 0)->first();
            if ($ideal) {
                Log::info('[Locator] AI routing selected ideal hospital (beds + ambulances)', [
                    'hospital_id'        => $ideal->id,
                    'available_units'    => $ideal->available_ambulances,
                    'available_beds'     => $ideal->available_beds,
                    'distance_km'        => round($ideal->distance, 2),
                ]);
                return $ideal;
            }

            // Pass 2: hospital WITH available ambulances (but maybe full beds)
            $withAmb = $results->filter(fn($h) => $h->available_ambulances > 0)->first();
            if ($withAmb) {
                Log::info('[Locator] AI routing selected hospital with ambulance (no beds)', [
                    'hospital_id'        => $withAmb->id,
                    'available_units'    => $withAmb->available_ambulances,
                    'distance_km'        => round($withAmb->distance, 2),
                ]);
                return $withAmb;
            }
            
            // Pass 3: hospital WITH available beds (but no available ambulances)
            $withBeds = $results->filter(fn($h) => $h->available_beds > 0)->first();
            if ($withBeds) {
                Log::info('[Locator] AI routing selected hospital with beds (no ambulances)', [
                    'hospital_id'        => $withBeds->id,
                    'available_beds'     => $withBeds->available_beds,
                    'distance_km'        => round($withBeds->distance, 2),
                ]);
                return $withBeds;
            }

            // Fallback: nearest hospital even if no ambulances or beds
            $nearest = $results->first();
            if ($nearest) {
                Log::warning('[Locator] No hospital with resources — assigned nearest anyway', [
                    'hospital_id' => $nearest->id,
                    'distance_km' => round($nearest->distance, 2),
                ]);
                return $nearest;
            }
        } else {
            // Standard routing: pure nearest
            $nearest = $query->orderBy('distance')->first();
            if ($nearest) {
                Log::info('[Locator] Standard routing selected nearest hospital', [
                    'hospital_id' => $nearest->id,
                    'distance_km' => round($nearest->distance, 2),
                ]);
                return $nearest;
            }
        }

        Log::error('[Locator] No eligible hospital found', [
            'lat'         => $lat,
            'lng'         => $lng,
            'excluded'    => $excludeIds,
            'max_radius'  => $maxRadius,
        ]);

        return null;
    }
}
