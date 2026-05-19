<?php

namespace App\Services\Emergency;

use App\Models\EmergencyRequest;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class EmergencySessionManager
{
    /**
     * Get the currently active emergency for a user or guest session.
     * EXCLUDES terminal statuses (completed, cancelled, rejected).
     */
    public function getActive(User $user = null, string $sessionId = null): ?EmergencyRequest
    {
        $query = EmergencyRequest::active();

        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return null;
        }

        return $query->latest()->first();
    }

    /**
     * Fully teardown a session, clearing all persistence layers.
     */
    public function teardown(EmergencyRequest $emergency): void
    {
        // 1. Clear Backend Session if present
        Session::forget('active_emergency_id');
        Session::forget('active_session_id');

        // 2. Clear caches or other persistence if needed
        // (Websocket markers and polling are handled frontend-side)
    }

    /**
     * Validate if an ID belongs to an active session.
     */
    public function isValidActive(int $id, string $sessionId = null): bool
    {
        $emergency = EmergencyRequest::find($id);
        if (!$emergency) return false;

        if ($emergency->isTerminal()) return false;

        if (auth()->check()) {
            return $emergency->user_id === auth()->id();
        }

        return $emergency->session_id === $sessionId;
    }
}
