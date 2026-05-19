<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    protected $fillable = [
        'hospital_id',
        'driver_name',
        'plate_number',
        'status',
        'fuel_level',
        'oxygen_level',
        'last_maintenance_at',
        'current_location',
        'current_latitude',
        'current_longitude',
        'last_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_completed_at'   => 'datetime',
            'last_maintenance_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Status constants — single source of truth
    // -------------------------------------------------------------------------
    const STATUS_AVAILABLE   = 'available';
    const STATUS_DISPATCHED  = 'dispatched';
    const STATUS_IN_TRANSIT  = 'in_transit';
    const STATUS_MAINTENANCE = 'maintenance';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function emergencyRequests()
    {
        return $this->hasMany(EmergencyRequest::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
}
