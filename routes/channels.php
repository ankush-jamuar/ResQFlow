<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('emergency.hospital.{hospitalId}', function ($user, $hospitalId) {
    \Log::info('WebSocket Auth attempt:', ['user_id' => $user->id, 'hospitalId' => $hospitalId, 'hospital_rel_id' => optional($user->hospital)->id]);
    return $user->role === 'hospital' && $user->hospital && $user->hospital->id == $hospitalId;
});

Broadcast::channel('admin.emergencies', function ($user) {
    return $user->role === 'admin';
});
