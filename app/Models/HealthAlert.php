<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthAlert extends Model
{
    protected $fillable = ['user_id', 'type', 'severity', 'message', 'meta_data', 'read_at'];

    protected $casts = [
        'meta_data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCritical(): bool
    {
        return $this->severity === 'CRITICAL';
    }
}
