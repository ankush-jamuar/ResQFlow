<?php

namespace App\Http\Controllers;

use App\Events\EmergencyCreated;
use App\Events\EmergencyStatusUpdated;
use App\Models\Ambulance;
use App\Models\EmergencyAudit;
use App\Models\EmergencyRequest;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\EmergencyAlertNotification;
use App\Services\HospitalLocatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyController extends Controller
{
    // ─── Strict state machine ─────────────────────────────────────────────────
    // Defines ONLY allowed transitions. Any other transition is rejected 422.
    private const ALLOWED_TRANSITIONS = [
        'pending'    => ['accepted'],
        'escalated'  => ['accepted'],
        'accepted'   => ['dispatched'],
        'dispatched' => ['en_route'],
        'en_route'   => ['arrived'],
        'arrived'    => ['completed'],
    ];

    public function __construct(
        private readonly HospitalLocatorService $locator,
        private readonly \App\Services\Emergency\EmergencySessionManager $sessionManager
    ) {}

    // =========================================================================
    // STORE — Create a new emergency request
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'emergency_type'   => 'nullable|string|max:100',
            'severity'         => 'required|in:low,medium,high',
            'family_member_id' => 'nullable|exists:family_members,id',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $userId    = auth()->id();
        $sessionId = $userId ? null : Str::uuid()->toString();

        // Safety: prevent multiple active emergencies
        $activeQuery = EmergencyRequest::whereIn('status', [
            'pending', 'accepted', 'dispatched', 'en_route', 'arrived',
        ]);

        if ($userId) {
            $activeQuery->where('user_id', $userId);
        } else {
            $activeQuery->where('session_id', session('active_session_id', $sessionId));
        }

        if ($activeQuery->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active emergency request.',
            ], 422);
        }

        // ── FIND BEST HOSPITAL ──
        $nearestHospital = $this->locator->nearest($lat, $lng);

        // Create emergency record regardless of hospital availability (Phase 3: Resiliency)
        $emergency = EmergencyRequest::create([
            'user_id'          => $userId,
            'family_member_id' => $request->family_member_id,
            'session_id'       => $sessionId,
            'hospital_id'      => $nearestHospital?->id,
            'latitude'         => $lat,
            'longitude'        => $lng,
            'emergency_type'   => $request->emergency_type,
            'severity'         => $request->severity,
            'status'           => $nearestHospital ? EmergencyRequest::STATUS_PENDING : 'escalated',
        ]);

        if (!$nearestHospital) {
            EmergencyAudit::create([
                'emergency_request_id' => $emergency->id,
                'user_id'              => null,
                'action'               => 'no_hospital_available',
                'old_state'            => null,
                'new_state'            => 'escalated',
                'notes'                => 'SYSTEM FAILURE: No hospitals found within operational radius. Escalated to Command Center.',
            ]);

            broadcast(new EmergencyCreated($emergency));
            
            return response()->json([
                'success'      => true,
                'emergency_id' => $emergency->id,
                'message'      => 'SIGNAL ESCALATED. All local units occupied. Command Center is manually coordinating your rescue.',
            ]);
        }

        if (!$userId) {
            session(['active_emergency_id' => $emergency->id]);
        }

        // ── INTELLIGENT AUTO-DISPATCH ──
        // High-priority intercept: for "high" severity, pick the hospital
        // with the MOST available ambulances (already handled in locator).
        // Now auto-assign an ambulance if one exists.
        $ambulance = Ambulance::where('hospital_id', $nearestHospital->id)
            ->where('status', Ambulance::STATUS_AVAILABLE)
            ->lockForUpdate()
            ->first();

        if ($ambulance) {
            DB::transaction(function () use ($emergency, $ambulance) {
                $ambulance->update(['status' => Ambulance::STATUS_DISPATCHED]);
                $emergency->update([
                    'ambulance_id' => $ambulance->id,
                    'status'       => EmergencyRequest::STATUS_ACCEPTED,
                ]);

                EmergencyAudit::create([
                    'emergency_request_id' => $emergency->id,
                    'user_id'              => null,
                    'action'               => 'auto_dispatch',
                    'old_state'            => 'pending',
                    'new_state'            => 'accepted',
                    'notes'                => "Auto-dispatched unit {$ambulance->plate_number}",
                ]);
            });

            Log::info('[Emergency] Auto-dispatched', [
                'emergency_id' => $emergency->id,
                'ambulance_id' => $ambulance->id,
            ]);
        }

        // Broadcast
        broadcast(new EmergencyCreated($emergency));
        if ($ambulance) {
            broadcast(new EmergencyStatusUpdated($emergency));
        }

        // Notify stakeholders
        $emergency->load(['hospital.user']);
        $this->notifyStakeholders($emergency, 'created');

        Log::info('[Emergency] SOS Protocol Triggered', [
            'emergency_id'   => $emergency->id,
            'hospital_id'    => $nearestHospital->id,
            'severity'       => $emergency->severity,
            'coordinates'    => [$lat, $lng],
            'ambulance'      => $ambulance?->plate_number ?? 'none (queued)',
            'auto_dispatched'=> (bool)$ambulance,
        ]);

        return response()->json([
            'success'      => true,
            'emergency_id' => $emergency->id,
            'session_id'   => $sessionId,
            'message'      => $ambulance
                ? 'Emergency verified. Unit dispatched.'
                : 'Emergency request received. Nearest hospital notified — awaiting unit dispatch.',
        ]);
    }

    // =========================================================================
    // TRACK — Poll emergency status (user-side map tracking)
    // =========================================================================
    public function track(Request $request)
    {
        $id        = $request->query('id');
        $sessionId = $request->query('session_id');

        // Self-heal: reset stale in_transit ambulances with no active emergency
        Ambulance::where('status', Ambulance::STATUS_IN_TRANSIT)
            ->whereDoesntHave('emergencyRequests', function ($q) {
                $q->whereIn('status', ['pending', 'accepted', 'dispatched', 'en_route', 'arrived']);
            })
            ->update(['status' => Ambulance::STATUS_AVAILABLE]);

        $isValid = $this->sessionManager->isValidActive($id, $sessionId);

        if (!$isValid) {
            // Check if it's just completed - we might still want to show the final response once
            $emergency = EmergencyRequest::find($id);
            if ($emergency && $emergency->status === 'completed') {
                return $this->buildTrackResponse($emergency);
            }
            
            return response()->json(['success' => false, 'message' => 'Emergency session expired or terminal.'], 404);
        }

        $emergency = EmergencyRequest::with(['hospital', 'ambulance'])->find($id);

        $terminalStatuses = ['completed', 'cancelled', 'rejected'];

        // Terminal status: return data but skip all processing
        if ($emergency->isCompleted() || in_array($emergency->status, $terminalStatuses)) {
            return $this->buildTrackResponse($emergency);
        }

        // ── LIVE ETA ENGINE ──
        // Calculate ETA even if just accepted/dispatched (from hospital to user)
        if ($emergency->ambulance) {
            $ambulance  = $emergency->ambulance;
            $currentLat = (float) ($ambulance->current_latitude  ?? $emergency->hospital?->latitude);
            $currentLng = (float) ($ambulance->current_longitude ?? $emergency->hospital?->longitude);
            $targetLat  = (float) $emergency->latitude;
            $targetLng  = (float) $emergency->longitude;

            $distance = 6371 * acos(
                max(-1, min(1, cos(deg2rad($targetLat)) * cos(deg2rad($currentLat)) *
                cos(deg2rad($currentLng) - deg2rad($targetLng)) +
                sin(deg2rad($targetLat)) * sin(deg2rad($currentLat))))
            );

            $eta = max(1, (int) ceil($distance * 2.0)); // Adjusted factor for realism
            if ($emergency->eta_minutes !== $eta) {
                $emergency->update(['eta_minutes' => $eta]);
            }
        }

        // LIVE AMBULANCE TRACKING (dispatched, en_route or arrived)
        if (in_array($emergency->status, [EmergencyRequest::STATUS_DISPATCHED, EmergencyRequest::STATUS_EN_ROUTE, 'arrived']) && $emergency->ambulance) {
            $ambulance  = $emergency->ambulance;
            $currentLat = (float) $ambulance->current_latitude;
            $currentLng = (float) $ambulance->current_longitude;
            $targetLat  = (float) $emergency->latitude;
            $targetLng  = (float) $emergency->longitude;

            $seconds = $ambulance->updated_at ? now()->diffInSeconds($ambulance->updated_at) : 5;
            $factor  = min(0.20 * (max($seconds, 1) / 5), 1); // Slightly faster movement for testing

            $newLat = $currentLat + ($targetLat - $currentLat) * $factor;
            $newLng = $currentLng + ($targetLng - $currentLng) * $factor;

            // Recalculate distance after movement
            $distance = 6371 * acos(
                max(-1, min(1, cos(deg2rad($targetLat)) * cos(deg2rad($newLat)) *
                cos(deg2rad($newLng) - deg2rad($targetLng)) +
                sin(deg2rad($targetLat)) * sin(deg2rad($newLat))))
            );

            if ($distance < 0.03) { // 30 meters
                // Ambulance arrived — transition to arrived state
                DB::transaction(function () use ($emergency, $ambulance, $targetLat, $targetLng) {
                    $locked = EmergencyRequest::whereKey($emergency->id)->lockForUpdate()->first();
                    if (!$locked || $locked->isCompleted() || in_array($locked->status, ['cancelled', 'rejected', 'arrived'])) return;

                    $ambulance->update([
                        'current_latitude'  => $targetLat,
                        'current_longitude' => $targetLng,
                    ]);

                    $oldStatus = $locked->status;
                    $locked->update(['status' => 'arrived', 'eta_minutes' => 0]);

                    Log::info('[Emergency] Proximity Arrival Triggered', [
                        'emergency_id' => $locked->id,
                        'distance_m'   => round($distance * 1000, 2),
                        'from'         => $oldStatus,
                        'to'           => 'arrived',
                    ]);

                    EmergencyAudit::create([
                        'emergency_request_id' => $locked->id,
                        'user_id'              => null,
                        'action'               => 'proximity_arrival',
                        'old_state'            => $oldStatus,
                        'new_state'            => 'arrived',
                        'notes'                => "Unit arrived at scene (GPS proximity < 30m).",
                    ]);
                });

                $emergency->refresh();
                broadcast(new EmergencyStatusUpdated($emergency));
                $this->notifyStakeholders($emergency, 'arrived');
            } else {
                $ambulance->update(['current_latitude' => $newLat, 'current_longitude' => $newLng]);
            }
        }



        return $this->buildTrackResponse($emergency);
    }

    // =========================================================================
    // CANCEL — User cancels the emergency
    // =========================================================================
    public function cancel(Request $request, EmergencyRequest $emergency)
    {
        if (auth()->check() && $emergency->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $activeStatuses = ['pending', 'accepted', 'dispatched', 'en_route', 'arrived'];
        if (!in_array($emergency->status, $activeStatuses)) {
            return response()->json(['success' => false, 'message' => 'Emergency already closed.'], 422);
        }

        DB::transaction(function () use ($emergency) {
            $locked = EmergencyRequest::whereKey($emergency->id)->lockForUpdate()->first();
            if (!$locked || !in_array($locked->status, ['pending', 'accepted', 'dispatched', 'en_route', 'arrived'])) return;

            if ($locked->ambulance_id) {
                $this->releaseAmbulance($locked->ambulance_id);
            }

            $oldStatus = $locked->status;
            $locked->update(['status' => 'cancelled']);

            EmergencyAudit::create([
                'emergency_request_id' => $locked->id,
                'user_id'              => auth()->id(),
                'action'               => 'cancelled',
                'old_state'            => $oldStatus,
                'new_state'            => 'cancelled',
                'notes'                => 'Mission aborted by user.',
            ]);

            broadcast(new EmergencyStatusUpdated($locked))->toOthers();
            $this->notifyStakeholders($locked, 'cancelled');

            Log::info('[Emergency] Mission Aborted', [
                'emergency_id' => $locked->id,
                'user_id'      => $locked->user_id,
                'from_status'  => $oldStatus,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Mission aborted successfully.']);
    }

    // =========================================================================
    // UPDATE STATUS — Hospital manages the lifecycle (strict state machine)
    // =========================================================================
    public function updateStatus(Request $request, EmergencyRequest $emergency)
    {
        $request->validate([
            'status'       => 'required|in:accepted,dispatched,en_route,arrived,completed',
            'ambulance_id' => 'nullable|exists:ambulances,id',
        ]);

        $newStatus   = $request->status;
        $ambulanceId = $request->ambulance_id;

        // Terminal guard
        $closedStatuses = ['completed', 'cancelled', 'rejected'];
        if (in_array($emergency->status, $closedStatuses)) {
            $msg = 'This emergency is closed and cannot be modified.';
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // Strict state machine enforcement
        $allowed = self::ALLOWED_TRANSITIONS[$emergency->status] ?? [];
        if (!in_array($newStatus, $allowed)) {
            $msg = "Invalid transition: {$emergency->status} → {$newStatus}. Allowed: " . implode(', ', $allowed ?: ['none']);
            Log::warning('[Emergency] Rejected invalid state transition', [
                'emergency_id' => $emergency->id,
                'from'         => $emergency->status,
                'to'           => $newStatus,
            ]);
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // Dispatching requires an ambulance
        if ($newStatus === 'dispatched' && !$ambulanceId && !$emergency->ambulance_id) {
            $msg = 'You must assign an ambulance before dispatching.';
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        DB::transaction(function () use (&$emergency, $newStatus, $ambulanceId) {
            $emergency = EmergencyRequest::whereKey($emergency->id)->lockForUpdate()->first();
            
            // Terminal guard again inside lock
            if (in_array($emergency->status, ['completed', 'cancelled', 'rejected'])) {
                return;
            }

            // Strict state machine enforcement inside lock
            $allowed = self::ALLOWED_TRANSITIONS[$emergency->status] ?? [];
            if (!in_array($newStatus, $allowed)) {
                return;
            }

            $this->handleAmbulanceAssignment($emergency, $ambulanceId);
            $this->applyStatusTransition($emergency, $newStatus);
        });

        $emergency->refresh();

        Log::info('[Emergency] Manual Lifecycle Transition', [
            'emergency_id' => $emergency->id,
            'hospital_id'  => $emergency->hospital_id,
            'from'         => $emergency->getOriginal('status'),
            'to'           => $newStatus,
            'ambulance'    => $emergency->ambulance?->plate_number,
        ]);

        broadcast(new EmergencyStatusUpdated($emergency));
        $this->notifyStakeholders($emergency, $newStatus);

        $msg = "Status updated to {$newStatus} successfully.";
        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => $msg, 'status' => $emergency->status])
            : redirect()->back()->with('success', $msg);
    }

    // =========================================================================
    // REJECT — Hospital rejects (reassign or terminal)
    // =========================================================================
    public function reject(Request $request, EmergencyRequest $emergency)
    {
        if (in_array($emergency->status, ['completed', 'cancelled', 'rejected'])) {
            $msg = 'This emergency is already closed.';
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        if ($emergency->status !== EmergencyRequest::STATUS_PENDING) {
            $msg = 'Cannot reject: emergency is no longer pending.';
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        $rejectedList = $emergency->rejected_hospitals ?? [];
        if (!in_array($emergency->hospital_id, $rejectedList)) {
            $rejectedList[] = $emergency->hospital_id;
        }

        $nextHospital = $this->locator->nearest(
            $emergency->latitude,
            $emergency->longitude,
            $rejectedList
        );

        if ($nextHospital) {
            $emergency->update([
                'hospital_id'        => $nextHospital->id,
                'rejected_hospitals' => $rejectedList,
            ]);

            EmergencyAudit::create([
                'emergency_request_id' => $emergency->id,
                'user_id'              => auth()->id(),
                'action'               => 'rejected_reassigned',
                'old_state'            => 'pending',
                'new_state'            => 'pending',
                'notes'                => "Rejected by hospital. Reassigned to {$nextHospital->name}.",
            ]);

            broadcast(new EmergencyCreated($emergency));
            broadcast(new EmergencyStatusUpdated($emergency));

            Log::info('[Emergency] Rejected and reassigned', [
                'emergency_id'   => $emergency->id,
                'new_hospital'   => $nextHospital->id,
                'rejected_count' => count($rejectedList),
            ]);

            $msg = 'Emergency rejected and reassigned to next nearest hospital.';
            return $request->wantsJson()
                ? response()->json(['success' => true, 'message' => $msg])
                : redirect()->back()->with('success', $msg);
        }

        // Terminal rejection — no hospitals left
        DB::transaction(function () use ($emergency, $rejectedList) {
            $locked = EmergencyRequest::whereKey($emergency->id)->lockForUpdate()->first();
            if (!$locked || in_array($locked->status, ['completed', 'cancelled', 'rejected'])) return;

            if ($locked->ambulance_id) {
                $this->releaseAmbulance($locked->ambulance_id);
            }

            $locked->update([
                'status'             => 'rejected',
                'rejected_hospitals' => $rejectedList,
            ]);

            EmergencyAudit::create([
                'emergency_request_id' => $locked->id,
                'user_id'              => auth()->id(),
                'action'               => 'terminal_rejection',
                'old_state'            => 'pending',
                'new_state'            => 'rejected',
                'notes'                => 'All hospitals exhausted. Mission terminally rejected.',
            ]);

            broadcast(new EmergencyStatusUpdated($locked));
        });

        Log::info('[Emergency] Terminally rejected', [
            'emergency_id'   => $emergency->id,
            'rejected_count' => count($rejectedList),
        ]);

        $msg = 'Emergency rejected. No more hospitals available.';
        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => $msg])
            : redirect()->back()->with('success', $msg);
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    private function buildTrackResponse(EmergencyRequest $emergency): \Illuminate\Http\JsonResponse
    {
        $criticalHealth = null;
        if ($emergency->user_id && $emergency->user?->medicalProfile) {
            $profile = $emergency->user->medicalProfile;
            $criticalHealth = [
                'blood_group' => $profile->blood_group,
                'allergies' => $profile->allergies,
                'chronic_conditions' => $profile->chronic_conditions,
                'medications' => $emergency->user->medications()->where('is_active', true)->pluck('name')->toArray(),
            ];
        }

        return response()->json([
            'success'              => true,
            'status'               => $emergency->status,
            'hospital'             => $emergency->hospital?->name,
            'ambulance'            => $emergency->ambulance?->plate_number,
            'hospital_lat'         => $emergency->hospital?->latitude,
            'hospital_lng'         => $emergency->hospital?->longitude,
            'latitude'             => $emergency->latitude,
            'longitude'            => $emergency->longitude,
            'assigned_hospital_id' => $emergency->hospital_id,
            'all_hospitals'        => \App\Models\Hospital::select('id', 'name', 'latitude', 'longitude')->get(),
            'ambulance_lat'        => $emergency->ambulance?->current_latitude,
            'ambulance_lng'        => $emergency->ambulance?->current_longitude,
            'eta_minutes'          => $emergency->eta_minutes,
            'critical_health'      => $criticalHealth,
        ]);
    }

    private function handleAmbulanceAssignment(EmergencyRequest $emergency, ?int $requestedAmbulanceId): void
    {
        if (!$requestedAmbulanceId || $requestedAmbulanceId == $emergency->ambulance_id) return;

        $newAmbulance = Ambulance::lockForUpdate()->find($requestedAmbulanceId);

        if (!$newAmbulance) {
            abort(422, 'Selected ambulance not found.');
        }

        if ($newAmbulance->status !== Ambulance::STATUS_AVAILABLE) {
            abort(422, 'The selected ambulance is no longer available.');
        }

        if ($emergency->ambulance_id) {
            $this->releaseAmbulance($emergency->ambulance_id);
        }

        $newAmbulance->update([
            'status'            => Ambulance::STATUS_DISPATCHED,
            'current_latitude'  => $emergency->hospital?->latitude,
            'current_longitude' => $emergency->hospital?->longitude,
        ]);

        Log::info('[Emergency] Unit Assigned', [
            'emergency_id' => $emergency->id,
            'ambulance_id' => $newAmbulance->id,
            'plate'        => $newAmbulance->plate_number,
        ]);

        $emergency->ambulance_id = $newAmbulance->id;
    }

    private function applyStatusTransition(EmergencyRequest $emergency, string $newStatus): void
    {
        $updates = ['status' => $newStatus];

        if ($newStatus === 'dispatched' && $emergency->ambulance_id) {
            $emergency->loadMissing('hospital');
            // Move ambulance to in_transit when physically dispatched
            Ambulance::where('id', $emergency->ambulance_id)
                ->where('status', Ambulance::STATUS_DISPATCHED)
                ->update([
                    'status'            => Ambulance::STATUS_IN_TRANSIT,
                    'current_latitude'  => $emergency->hospital?->latitude,
                    'current_longitude' => $emergency->hospital?->longitude,
                ]);
        }

        if ($newStatus === EmergencyRequest::STATUS_EN_ROUTE && $emergency->ambulance_id) {
            $emergency->loadMissing('hospital');
            Ambulance::where('id', $emergency->ambulance_id)
                ->where('status', Ambulance::STATUS_DISPATCHED)
                ->update([
                    'status'            => Ambulance::STATUS_IN_TRANSIT,
                    'current_latitude'  => $emergency->hospital?->latitude,
                    'current_longitude' => $emergency->hospital?->longitude,
                ]);
        }

        if ($newStatus === EmergencyRequest::STATUS_COMPLETED) {
            $updates['completed_at'] = now();
            if ($emergency->ambulance_id) {
                Ambulance::where('id', $emergency->ambulance_id)->update([
                    'status'            => Ambulance::STATUS_AVAILABLE,
                    'last_completed_at' => now(),
                ]);
            }
            // Centralized Teardown
            $this->sessionManager->teardown($emergency);
        }

        $updates['ambulance_id'] = $emergency->ambulance_id;
        $oldStatus = $emergency->status;

        $emergency->update($updates);

        EmergencyAudit::create([
            'emergency_request_id' => $emergency->id,
            'user_id'              => auth()->id(),
            'action'               => 'status_change',
            'old_state'            => $oldStatus,
            'new_state'            => $newStatus,
            'notes'                => "Transition: {$oldStatus} → {$newStatus}.",
        ]);
    }

    private function releaseAmbulance(int $ambulanceId): void
    {
        Ambulance::where('id', $ambulanceId)
            ->whereIn('status', [Ambulance::STATUS_DISPATCHED, Ambulance::STATUS_IN_TRANSIT])
            ->update([
                'status'            => Ambulance::STATUS_AVAILABLE,
                'last_completed_at' => now(),
            ]);
    }

    private function notifyStakeholders(EmergencyRequest $emergency, string $eventType): void
    {
        $pushEnabled = (bool) SystemSetting::get('push_notifications', true);
        if (!$pushEnabled) return;

        try {
            $hospitalUser = $emergency->hospital?->user;
            if ($hospitalUser) {
                $hospitalUser->notify(new EmergencyAlertNotification($emergency, $eventType));
            }

            if (in_array($eventType, ['created', 'cancelled', 'rejected'])) {
                User::where('role', 'admin')->each(function ($admin) use ($emergency, $eventType) {
                    $admin->notify(new EmergencyAlertNotification($emergency, $eventType));
                });
            }

            if ($emergency->user_id && in_array($eventType, ['accepted', 'dispatched', 'en_route', 'arrived', 'completed'])) {
                $emergency->user?->notify(new EmergencyAlertNotification($emergency, $eventType));
            }
        } catch (\Exception $e) {
            Log::warning('[Notification] Failed', ['error' => $e->getMessage()]);
        }
    }
}
