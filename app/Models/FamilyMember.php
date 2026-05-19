<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'blood_group',
        'allergies',
        'chronic_conditions',
        'contact_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalProfile()
    {
        return $this->hasOne(MedicalProfile::class);
    }

    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class)->latest();
    }

    public function healthIndicators()
    {
        return $this->hasMany(HealthIndicator::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function healthSnapshots()
    {
        return $this->hasMany(HealthIntelligenceSnapshot::class);
    }

    public function healthAlerts()
    {
        return $this->hasMany(HealthAlert::class);
    }

    public function aiRecommendations()
    {
        return $this->hasMany(AIRecommendation::class);
    }
}
