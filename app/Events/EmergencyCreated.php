<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmergencyCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $emergency;

    /**
     * Create a new event instance.
     */
    public function __construct($emergency)
    {
        $this->emergency = $emergency;
        \Log::info('Broadcasting EmergencyCreated event', ['emergency_id' => $emergency->id, 'hospital_id' => $emergency->hospital_id]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // NOTE: Laravel/Reverb auto-prepends 'private-' for PrivateChannel.
        // So 'emergency.hospital.1' becomes 'private-emergency.hospital.1' on the wire.
        return [
            new PrivateChannel('emergency.hospital.' . $this->emergency->hospital_id),
            new PrivateChannel('admin.emergencies'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->emergency->id,
            'severity' => $this->emergency->severity,
            'status' => $this->emergency->status,
            'latitude' => $this->emergency->latitude,
            'longitude' => $this->emergency->longitude,
            'created_at' => $this->emergency->created_at->toIso8601String(),
            'created_at_human' => $this->emergency->created_at->diffForHumans(),
            'hospital_id' => $this->emergency->hospital_id,
        ];
    }
}
