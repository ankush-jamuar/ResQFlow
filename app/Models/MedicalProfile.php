<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'family_member_id',
        'blood_group',
        'date_of_birth',
        'allergies',
        'chronic_conditions',
        'current_medications',
        'weight_kg',
        'height_cm',
        'is_manual_verified',
        'last_ai_sync_at',
        'sync_source'
    ];
 
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'weight_kg'     => 'decimal:2',
            'height_cm'     => 'decimal:2',
            'is_manual_verified' => 'boolean',
            'last_ai_sync_at' => 'datetime'
        ];
    }
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }
}
