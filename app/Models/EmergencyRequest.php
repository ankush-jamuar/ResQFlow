<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'family_member_id',
        'session_id',
        'hospital_id',
        'ambulance_id',
        'status',
        'completed_at',
        'emergency_type',
        'severity',
        'latitude',
        'longitude',
        'eta_minutes',
        'rejected_hospitals',
    ];

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    protected function casts(): array
    {
        return [
            'completed_at'       => 'datetime',
            'rejected_hospitals' => 'array',
        ];
    }

    // -------------------------------------------------------------------------
    // Status constants
    // -------------------------------------------------------------------------
    const STATUS_PENDING    = 'pending';
    const STATUS_ACCEPTED   = 'accepted';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_EN_ROUTE   = 'en_route';
    const STATUS_ARRIVED    = 'arrived';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_ESCALATED  = 'escalated';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    public function audits()
    {
        return $this->hasMany(EmergencyAudit::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_DISPATCHED,
            self::STATUS_EN_ROUTE,
            self::STATUS_ARRIVED,
            self::STATUS_ESCALATED,
        ]);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['completed', 'cancelled', 'rejected']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_DISPATCHED,
            self::STATUS_EN_ROUTE,
            self::STATUS_ARRIVED,
            self::STATUS_ESCALATED,
        ]);
    }

    public function scopeTerminal($query)
    {
        return $query->whereIn('status', ['completed', 'cancelled', 'rejected']);
    }
}
