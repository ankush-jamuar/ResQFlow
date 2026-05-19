<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'contact',
        'available_beds',
        'user_id',
        'is_dynamic',
        'warning_points',
        'reliability_score',
        'is_suspended',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_suspended' => 'boolean',
            'reliability_score' => 'decimal:1',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ambulances()
    {
        return $this->hasMany(Ambulance::class);
    }

    public function emergencyRequests()
    {
        return $this->hasMany(EmergencyRequest::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function availableAmbulancesCount(): int
    {
        return $this->ambulances()->where('status', 'available')->count();
    }

    public function reliabilityColor(): string
    {
        if ($this->reliability_score >= 80) return 'emerald';
        if ($this->reliability_score >= 50) return 'amber';
        return 'red';
    }
}
