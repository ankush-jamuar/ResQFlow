<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\EmergencyRequest;
use App\Models\Hospital;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalController extends Controller
{
    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard()
    {
        $hospital = auth()->user()->hospital;
        if (!$hospital) return redirect('/')->with('error', 'Hospital profile not found.');

        $emergencies = EmergencyRequest::where('hospital_id', $hospital->id)
            ->whereIn('status', ['pending', 'accepted', 'dispatched', 'en_route', 'arrived'])
            ->orderByRaw("FIELD(severity, 'high', 'medium', 'low')")
            ->latest()->take(5)->get();

        $ambulances = Ambulance::where('hospital_id', $hospital->id)->get();
        $resources  = $hospital->resources;

        $statsToday   = EmergencyRequest::where('hospital_id', $hospital->id)->whereDate('created_at', today())->count();
        $statsActive  = EmergencyRequest::where('hospital_id', $hospital->id)->whereIn('status', ['pending', 'accepted', 'dispatched', 'en_route', 'arrived'])->count();
        $statsCompleted = EmergencyRequest::where('hospital_id', $hospital->id)->where('status', 'completed')->count();
        $availableUnits = Ambulance::where('hospital_id', $hospital->id)->where('status', 'available')->count();

        return view('dashboard.hospital', compact(
            'hospital', 'emergencies', 'ambulances', 'resources',
            'statsToday', 'statsActive', 'statsCompleted', 'availableUnits'
        ));
    }

    // =========================================================================
    // FLEET MANAGEMENT
    // =========================================================================
    public function fleet()
    {
        $hospital   = auth()->user()->hospital;
        $ambulances = Ambulance::where('hospital_id', $hospital->id)->paginate(15);
        return view('hospital.fleet.index', compact('ambulances', 'hospital'));
    }

    public function storeAmbulance(Request $request)
    {
        $request->validate([
            'driver_name'  => 'required|string|max:100',
            'plate_number' => 'required|string|max:20|unique:ambulances,plate_number',
        ]);

        $hospital = auth()->user()->hospital;
        Ambulance::create([
            'hospital_id'       => $hospital->id,
            'driver_name'       => $request->driver_name,
            'plate_number'      => strtoupper($request->plate_number),
            'status'            => 'available',
            'current_latitude'  => $hospital->latitude,
            'current_longitude' => $hospital->longitude,
        ]);

        return redirect()->route('hospital.fleet')->with('success', 'Rescue unit enlisted successfully.');
    }

    public function updateAmbulance(Request $request, Ambulance $ambulance)
    {
        // Ensure the ambulance belongs to this hospital
        abort_unless($ambulance->hospital_id === auth()->user()->hospital?->id, 403);

        $request->validate([
            'driver_name'  => 'required|string|max:100',
            'plate_number' => 'required|string|max:20|unique:ambulances,plate_number,' . $ambulance->id,
        ]);

        $ambulance->update($request->only('driver_name', 'plate_number'));
        return redirect()->route('hospital.fleet')->with('success', 'Unit updated successfully.');
    }

    public function logMaintenance(Ambulance $ambulance)
    {
        abort_unless($ambulance->hospital_id === auth()->user()->hospital?->id, 403);

        if ($ambulance->status === Ambulance::STATUS_MAINTENANCE) {
            $ambulance->update([
                'status'              => Ambulance::STATUS_AVAILABLE,
                'last_maintenance_at' => now(),
                'fuel_level'          => 100,
                'oxygen_level'        => 100,
            ]);
            $msg = "Unit {$ambulance->plate_number} is now back ONLINE. Telemetry reset.";
        } else {
            if ($ambulance->status !== Ambulance::STATUS_AVAILABLE) {
                return redirect()->back()->with('error', 'Cannot perform maintenance while unit is on a mission.');
            }
            $ambulance->update(['status' => Ambulance::STATUS_MAINTENANCE]);
            $msg = "Unit {$ambulance->plate_number} is now in MAINTENANCE MODE and offline.";
        }

        return redirect()->route('hospital.fleet')->with('success', $msg);
    }

    public function destroyAmbulance(Ambulance $ambulance)
    {
        abort_unless($ambulance->hospital_id === auth()->user()->hospital?->id, 403);

        if ($ambulance->status !== 'available') {
            return redirect()->route('hospital.fleet')->with('error', 'Cannot decommission a unit that is currently on a mission.');
        }

        $ambulance->delete();
        return redirect()->route('hospital.fleet')->with('success', 'Unit decommissioned.');
    }

    // =========================================================================
    // DRIVERS (Managed through Ambulances)
    // =========================================================================
    public function drivers()
    {
        $hospital = auth()->user()->hospital;
        $drivers  = Ambulance::where('hospital_id', $hospital->id)->get();
        return view('hospital.drivers.index', compact('drivers', 'hospital'));
    }

    // =========================================================================
    // RESOURCE MANAGEMENT
    // =========================================================================
    public function resources()
    {
        $hospital  = auth()->user()->hospital;
        $resources = $hospital->resources;
        return view('hospital.resources.index', compact('resources', 'hospital'));
    }

    public function updateResource(Request $request, Resource $resource)
    {
        abort_unless($resource->hospital_id === auth()->user()->hospital?->id, 403);

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $resource->update(['quantity' => $request->quantity]);
        return redirect()->route('hospital.resources')->with('success', 'Resource updated.');
    }

    public function storeResource(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'type'     => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
        ]);

        $hospital = auth()->user()->hospital;
        Resource::create([
            'hospital_id' => $hospital->id,
            'name'        => $request->name,
            'type'        => $request->type,
            'quantity'    => $request->quantity,
        ]);

        return redirect()->route('hospital.resources')->with('success', 'Resource provisioned.');
    }

    // =========================================================================
    // DISPATCH QUEUE
    // =========================================================================
    public function queue(Request $request)
    {
        $hospital  = auth()->user()->hospital;
        $query     = EmergencyRequest::where('hospital_id', $hospital->id)->with('ambulance', 'user', 'familyMember')->latest();
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $emergencies = $query->paginate(15)->withQueryString();
        return view('hospital.queue.index', compact('emergencies', 'hospital'));
    }

    // =========================================================================
    // LATEST EMERGENCIES (API for dashboard polling)
    // =========================================================================
    public function latestEmergencies()
    {
        $hospital = auth()->user()->hospital;
        if (!$hospital) return response()->json(['error' => 'Unauthorized'], 403);

        $emergencies = EmergencyRequest::where('hospital_id', $hospital->id)
            ->whereIn('status', ['pending'])
            ->orderByRaw("FIELD(severity, 'high', 'medium', 'low')")
            ->latest()->take(10)->get()
            ->map(fn($e) => [
                'id'               => $e->id,
                'severity'         => $e->severity,
                'status'           => $e->status,
                'latitude'         => $e->latitude,
                'longitude'        => $e->longitude,
                'created_at'       => $e->created_at->toIso8601String(),
                'created_at_human' => $e->created_at->diffForHumans(),
                'hospital_id'      => $e->hospital_id,
            ]);

        return response()->json(['emergencies' => $emergencies]);
    }
}
