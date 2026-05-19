<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthIndicator extends Model
{
    protected $fillable = [
        'user_id', 'family_member_id', 'key', 'value', 'unit', 'measured_at', 
        'confidence_score', 'source_type'
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'confidence_score' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }
}
