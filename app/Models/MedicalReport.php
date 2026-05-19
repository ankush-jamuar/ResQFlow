<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{
    protected $fillable = [
        'user_id', 'family_member_id', 'file_name', 'file_path', 'parsed_data', 
        'extraction_metadata', 'ai_summary', 'status', 'error_reason', 
        'confidence_score', 'processed_at'
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'extraction_metadata' => 'array',
        'confidence_score' => 'float',
        'processed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function recommendations()
    {
        return $this->hasMany(AIRecommendation::class, 'source_report_id');
    }

    public function getSignedUrl(): string
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'medical.reports.download',
            now()->addMinutes(30),
            ['report' => $this->id]
        );
    }

    public function getPreviewUrl(): string
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'medical.reports.preview',
            now()->addMinutes(30),
            ['report' => $this->id]
        );
    }
}
