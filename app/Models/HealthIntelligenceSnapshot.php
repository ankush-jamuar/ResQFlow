<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthIntelligenceSnapshot extends Model
{
    protected $fillable = ['user_id', 'type', 'data', 'version'];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
