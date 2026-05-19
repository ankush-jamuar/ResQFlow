<?php

namespace App\Notifications;

use App\Models\EmergencyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmergencyAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        private EmergencyRequest $emergency,
        private string $eventType
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toDatabase($notifiable): array
    {
        $titles = [
            'created'   => '🚨 New Emergency Incoming',
            'accepted'  => '✅ Emergency Accepted',
            'en_route'  => '🚑 Unit En Route',
            'completed' => '🏁 Mission Complete',
            'cancelled' => '❌ Mission Aborted',
            'rejected'  => '⚠️ Emergency Rejected',
        ];

        $messages = [
            'created'   => "Severity: " . strtoupper($this->emergency->severity) . " — Emergency #" . str_pad($this->emergency->id, 6, '0', STR_PAD_LEFT) . " requires immediate dispatch.",
            'accepted'  => "Emergency #" . str_pad($this->emergency->id, 6, '0', STR_PAD_LEFT) . " has been accepted by " . ($this->emergency->hospital->name ?? 'hospital') . ".",
            'en_route'  => "Unit " . ($this->emergency->ambulance->plate_number ?? 'ALPHA') . " is now en route. ETA: " . ($this->emergency->eta_minutes ?? '--') . " minutes.",
            'completed' => "Emergency #" . str_pad($this->emergency->id, 6, '0', STR_PAD_LEFT) . " resolved. Patient delivered successfully.",
            'cancelled' => "Emergency #" . str_pad($this->emergency->id, 6, '0', STR_PAD_LEFT) . " was aborted by the user.",
            'rejected'  => "Emergency #" . str_pad($this->emergency->id, 6, '0', STR_PAD_LEFT) . " was rejected. Reassigning to next hospital.",
        ];

        return [
            'emergency_id' => $this->emergency->id,
            'event_type'   => $this->eventType,
            'title'        => $titles[$this->eventType] ?? '🔔 Emergency Update',
            'message'      => $messages[$this->eventType] ?? 'An emergency status has changed.',
            'severity'     => $this->emergency->severity,
            'hospital'     => $this->emergency->hospital?->name,
            'status'       => $this->emergency->status,
        ];
    }
}
