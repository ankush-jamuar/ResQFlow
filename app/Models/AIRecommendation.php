<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIRecommendation extends Model
{
    protected $table = 'ai_recommendations';

    protected $fillable = [
        'user_id', 'family_member_id', 'content', 'type', 'confidence_score', 
        'model_used', 'reasoning_trace', 'prompt_version', 'source_report_id',
        'rationale', 'supporting_evidence', 'stability_hash', 
        'parent_recommendation_id', 'is_acknowledged', 'confidence_language'
    ];

    protected $casts = [
        'supporting_evidence' => 'array',
        'is_acknowledged' => 'boolean',
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

    public function sourceReport()
    {
        return $this->belongsTo(MedicalReport::class, 'source_report_id');
    }
}
