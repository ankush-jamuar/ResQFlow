<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\EmergencyAudit;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function show($id)
    {
        $emergency = EmergencyRequest::with(['user', 'ambulance', 'hospital', 'familyMember'])
            ->findOrFail($id);

        $audits = EmergencyAudit::where('emergency_request_id', $id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.incidents.show', compact('emergency', 'audits'));
    }
}
