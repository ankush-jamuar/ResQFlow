<?php

namespace App\Notifications;

use App\Models\Hospital;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminWarningNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Hospital $hospital,
        private string $reason
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'       => '⚠️ Official Admin Warning Issued',
            'message'     => "Your facility ({$this->hospital->name}) has received a formal warning from the ResQFlow Control Authority. Reason: {$this->reason}. Current warning points: {$this->hospital->warning_points}. Continued non-compliance may result in suspension.",
            'event_type'  => 'admin_warning',
            'hospital_id' => $this->hospital->id,
            'severity'    => 'high',
        ];
    }
}
