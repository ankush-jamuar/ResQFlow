<?php

namespace App\Events;

use App\Models\EmergencyRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmergencyStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $emergency;

    /**
     * Create a new event instance.
     */
    public function __construct(EmergencyRequest $emergency)
    {
        $this->emergency = $emergency;
        
        \Log::info('[Realtime] Broadcasting EmergencyStatusUpdated', [
            'emergency_id' => $emergency->id,
            'status'       => $emergency->status,
            'hospital'     => $emergency->hospital_id,
            'ambulance'    => $emergency->ambulance_id,
            'channels'     => [
                'public'   => 'emergency.tracking.' . $emergency->id,
                'hospital' => 'private-emergency.hospital.' . $emergency->hospital_id,
                'admin'    => 'private-admin.emergencies'
            ]
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('emergency.tracking.' . $this->emergency->id),
            new PrivateChannel('admin.emergencies'),
            new PrivateChannel('emergency.hospital.' . $this->emergency->hospital_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->emergency->id,
            'status' => $this->emergency->status,
            'hospital' => $this->emergency->hospital ? $this->emergency->hospital->name : null,
            'ambulance' => $this->emergency->ambulance ? $this->emergency->ambulance->plate_number : null,
            'latitude' => $this->emergency->latitude,
            'longitude' => $this->emergency->longitude,
            'hospital_lat' => $this->emergency->hospital ? $this->emergency->hospital->latitude : null,
            'hospital_lng' => $this->emergency->hospital ? $this->emergency->hospital->longitude : null,
        ];
    }
}
