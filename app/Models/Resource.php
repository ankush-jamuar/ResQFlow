<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'hospital_id', 'type', 'quantity', 'unit'
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
