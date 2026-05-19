<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAudit extends Model
{
    protected $fillable = [
        'emergency_request_id',
        'user_id',
        'action',
        'old_state',
        'new_state',
        'notes',
    ];

    public function emergencyRequest()
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
