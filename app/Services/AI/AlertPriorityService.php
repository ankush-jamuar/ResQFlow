<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\HealthAlert;
use Illuminate\Support\Facades\Log;

class AlertPriorityService
{
    /**
     * Create a health alert with proper priority.
     */
    public function trigger(User $user, string $type, string $message, string $severity = 'INFO', array $meta = []): HealthAlert
    {
        $alert = $user->healthAlerts()->create([
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'meta_data' => $meta,
        ]);

        if ($severity === 'CRITICAL' || $severity === 'HIGH_RISK') {
            $this->notifyResponders($user, $alert);
        }

        return $alert;
    }

    protected function notifyResponders(User $user, HealthAlert $alert): void
    {
        // Integration with real-time websocket systems for responders
        // In ResQFlow, this would broadcast an event to active emergency dispatchers if the user is in SOS mode.
        Log::info("Critical Health Alert triggered for User #{$user->id}: {$alert->message}");
        
        // Broadcast logic here (e.g., broadcast(new CriticalHealthAlertEvent($user, $alert)))
    }
}
