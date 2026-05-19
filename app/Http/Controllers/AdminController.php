<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\EmergencyAudit;
use App\Models\EmergencyRequest;
use App\Models\Hospital;
use App\Models\User;
use App\Notifications\EmergencyAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // =========================================================================
    // STRATEGIC COMMAND DASHBOARD
    // =========================================================================
    public function dashboard(Request $request)
    {
        $hospitalsCount   = Hospital::count();
        $emergenciesCount = EmergencyRequest::count();
        $totalToday       = EmergencyRequest::whereDate('created_at', today())->count();
        $completedCount   = EmergencyRequest::where('status', 'completed')->count();
        $activeCount      = EmergencyRequest::whereIn('status', ['pending', 'accepted', 'en_route'])->count();
        $suspendedCount   = Hospital::where('is_suspended', true)->count();

        $avgResponseMinutes = (int) round(
            EmergencyRequest::whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_time')
                ->value('avg_time') ?? 0
        );

        $statusStats = EmergencyRequest::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
        $dailyStats  = EmergencyRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')->orderBy('date')->get();

        $recentEmergencies = EmergencyRequest::with(['user', 'hospital', 'ambulance'])->latest()->take(5)->get();

        // Negligence watch list — hospitals with high warning points
        $negligenceWatch = Hospital::where('warning_points', '>', 0)
            ->orderByDesc('warning_points')->take(5)->get();

        $activeEmergenciesList = EmergencyRequest::with(['ambulance', 'hospital', 'user'])->whereIn('status', ['pending', 'accepted', 'dispatched', 'en_route', 'arrived', 'escalated'])->get();

        // Heatmap data: all historical locations
        $heatmapData = EmergencyRequest::select('latitude', 'longitude', 'severity')->get()->map(function($em) {
            $weight = match($em->severity) {
                'high' => 1.0,
                'medium' => 0.6,
                'low' => 0.3,
                default => 0.1
            };
            return [(float)$em->latitude, (float)$em->longitude, $weight];
        });

        return view('dashboard.admin', compact(
            'hospitalsCount', 'emergenciesCount', 'totalToday', 'completedCount',
            'activeCount', 'avgResponseMinutes', 'statusStats', 'dailyStats',
            'recentEmergencies', 'suspendedCount', 'negligenceWatch', 'activeEmergenciesList',
            'heatmapData'
        ));
    }

    // =========================================================================
    // HOSPITAL MANAGEMENT
    // =========================================================================
    public function hospitals(Request $request)
    {
        $query = Hospital::with('user')->withCount('emergencyRequests');
        if ($request->filled('search')) $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->filled('city'))   $query->where('address', 'like', '%' . $request->city . '%');
        if ($request->filled('status') && $request->status === 'suspended') $query->where('is_suspended', true);

        $hospitals  = $query->orderByDesc('emergency_requests_count')->paginate(15);
        $citiesList = Hospital::selectRaw("TRIM(SUBSTRING_INDEX(address, ',', 1)) as city")->distinct()->pluck('city');

        return view('admin.hospitals.index', compact('hospitals', 'citiesList'));
    }

    // ──────────────── AUTHORITY ACTIONS ──────────────────────────────

    /**
     * Issue a formal warning to a hospital.
     */
    public function issueWarning(Request $request, Hospital $hospital)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $hospital->increment('warning_points');
        $hospital->decrement('reliability_score', 5.0);
        $hospital->update(['admin_notes' => $request->reason]);

        Log::info('[Admin] Issued Warning', ['hospital_id' => $hospital->id, 'reason' => $request->reason]);

        // Notify hospital user
        if ($hospital->user) {
            $hospital->user->notify(new \App\Notifications\AdminWarningNotification($hospital, $request->reason));
        }

        Log::warning('[Admin] Warning issued', ['hospital_id' => $hospital->id, 'reason' => $request->reason]);
        return back()->with('success', "Warning issued to {$hospital->name}. Points: {$hospital->warning_points}");
    }

    /**
     * Suspend a hospital from receiving new emergencies.
     */
    public function suspendHospital(Hospital $hospital)
    {
        $hospital->update(['is_suspended' => true]);
        Log::warning('[Admin] Hospital suspended', ['hospital_id' => $hospital->id]);
        return back()->with('success', "{$hospital->name} suspended from receiving new emergencies.");
    }

    /**
     * Reinstate a suspended hospital.
     */
    public function reinstateHospital(Hospital $hospital)
    {
        $hospital->update(['is_suspended' => false, 'warning_points' => 0]);
        Log::info('[Admin] Hospital reinstated', ['hospital_id' => $hospital->id]);
        return back()->with('success', "{$hospital->name} reinstated to active network.");
    }

    /**
     * Force reassign a stalled emergency to another hospital.
     */
    public function forceReassign(Request $request, EmergencyRequest $emergency)
    {
        $request->validate(['hospital_id' => 'required|exists:hospitals,id']);

        $newHospital = Hospital::findOrFail($request->hospital_id);
        
        $oldHospitalId = $emergency->hospital_id;
        $emergency->update(['hospital_id' => $newHospital->id, 'status' => EmergencyRequest::STATUS_PENDING]);

        EmergencyAudit::create([
            'emergency_request_id' => $emergency->id,
            'user_id'              => auth()->id(),
            'action'               => 'force_reassigned',
            'old_state'            => "hospital_{$oldHospitalId}",
            'new_state'            => "hospital_{$newHospital->id}",
            'notes'                => "Admin forced reassignment to {$newHospital->name}.",
        ]);

        broadcast(new \App\Events\EmergencyCreated($emergency));
        Log::info('[Admin] Force reassigned emergency', [
            'emergency_id'   => $emergency->id,
            'new_hospital_id'=> $newHospital->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Emergency #{$emergency->id} reassigned to {$newHospital->name}."]);
        }
        return back()->with('success', "Emergency #{$emergency->id} reassigned to {$newHospital->name}.");
    }

    // =========================================================================
    // AMBULANCE / FLEET OVERVIEW
    // =========================================================================
    public function ambulances(Request $request)
    {
        $query = Ambulance::with('hospital');
        if ($request->filled('status')) $query->where('status', $request->status);
        $ambulances = $query->paginate(20)->withQueryString();
        return view('admin.ambulances.index', compact('ambulances'));
    }

    // =========================================================================
    // EMERGENCY FEED
    // =========================================================================
    public function emergencies(Request $request)
    {
        $query = EmergencyRequest::with('user', 'hospital', 'ambulance')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $emergencies = $query->paginate(20)->withQueryString();

        // Available hospitals for force-reassign
        $availableHospitals = Hospital::where('is_suspended', false)
            ->orderBy('name')->get(['id', 'name']);

        return view('admin.emergencies.index', compact('emergencies', 'availableHospitals'));
    }

    // =========================================================================
    // ANALYTICS — Real data
    // =========================================================================
    public function analytics()
    {
        $severityStats = EmergencyRequest::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')->pluck('count', 'severity');

        $cityStats = Hospital::selectRaw("TRIM(SUBSTRING_INDEX(address, ' ', 1)) as city, COUNT(emergency_requests.id) as total")
            ->leftJoin('emergency_requests', 'hospitals.id', '=', 'emergency_requests.hospital_id')
            ->groupBy('city')->orderByDesc('total')->limit(10)->get();

        $avgResponseTime = (int) round(
            EmergencyRequest::whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg')
                ->value('avg') ?? 0
        );

        $dailyTrend = EmergencyRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(status="completed") as completed')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')->orderBy('date')->get();

        $topHospitals = Hospital::withCount('emergencyRequests')
            ->orderByDesc('emergency_requests_count')->take(10)->get();

        $negligenceLeague = Hospital::where('warning_points', '>', 0)
            ->orderByDesc('warning_points')->take(10)->get();

        return view('admin.analytics', compact(
            'severityStats', 'cityStats', 'avgResponseTime',
            'dailyTrend', 'topHospitals', 'negligenceLeague'
        ));
    }

    // =========================================================================
    // SETTINGS
    // =========================================================================
    public function settings()
    {
        return view('admin.settings');
    }

    // =========================================================================
    // IMPERSONATION
    // =========================================================================
    public function loginAsHospital(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $targetUser = User::findOrFail($request->user_id);

        if ($targetUser->role !== 'hospital') {
            return redirect()->back()->with('error', 'Selected user is not a hospital account.');
        }

        session(['admin_impersonator_id' => auth()->id()]);
        auth()->login($targetUser);

        return redirect()->route('hospital.dashboard')
            ->with('success', 'You are now logged in as ' . $targetUser->name);
    }

    public function returnToAdmin()
    {
        $adminId = session('admin_impersonator_id');
        if (!$adminId) return redirect()->route('admin.dashboard')->with('error', 'No impersonation session found.');

        $admin = User::find($adminId);
        if (!$admin || $admin->role !== 'admin') {
            session()->forget('admin_impersonator_id');
            return redirect('/')->with('error', 'Original admin account not found.');
        }

        auth()->login($admin);
        session()->forget('admin_impersonator_id');

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->name . '.');
    }

    // =========================================================================
    // AUDITS
    // =========================================================================
    public function audits()
    {
        $audits = EmergencyAudit::with(['emergencyRequest', 'user'])
            ->latest()
            ->paginate(50);
            
        return view('admin.audits', compact('audits'));
    }
}