<?php

namespace App\Http\Controllers;

use App\Models\EmergencyRequest;
use App\Models\FamilyMember;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard(\App\Services\Emergency\EmergencySessionManager $sessionManager)
    {
        $activeEmergency = $sessionManager->getActive(auth()->user());

        return view('dashboard.user', compact('activeEmergency'));
    }

    public function history()
    {
        $history = EmergencyRequest::where('user_id', auth()->id())
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->with(['hospital', 'ambulance'])
            ->latest()
            ->paginate(10);

        return view('user.history.index', compact('history'));
    }

    // =========================================================================
    // FAMILY HEALTH ECOSYSTEM
    // =========================================================================

    public function medicalProfile(
        \App\Services\AI\HealthRiskEngine $riskEngine,
        \App\Services\AI\HealthTrendEngine $trendEngine,
        \App\Services\AI\FamilyHealthGraph $familyGraph
    ) {
        $user = auth()->user();
        
        // Eager load all health ecosystem data with explicit sorting
        $user->load([
            'medicalProfile', 
            'familyMembers.medicalProfile',
            'medications' => fn($q) => $q->latest(),
            'healthIndicators' => fn($q) => $q->latest('measured_at'),
            'aiRecommendations' => fn($q) => $q->latest(),
            'medicalReports' => fn($q) => $q->latest() // CRITICAL: Fixes Document Sorting
        ]);
        
        // AUTHENTIC INTELLIGENCE COMPUTATION
        $risks = $riskEngine->calculateRisks($user);
        $trends = $trendEngine->getTrends($user);
        $inherited = $familyGraph->getInheritedRisks($user);
        
        $latestRecommendation = $user->aiRecommendations->first();

        return view('user.profile.medical', compact('user', 'risks', 'trends', 'inherited', 'latestRecommendation'));
    }

    public function updateMedicalProfile(Request $request)
    {
        $data = $request->validate([
            'blood_group'         => 'nullable|string|max:5',
            'date_of_birth'       => 'nullable|date',
            'allergies'           => 'nullable|string',
            'chronic_conditions'  => 'nullable|string',
            'current_medications' => 'nullable|string',
            'weight_kg'           => 'nullable|numeric|min:0',
            'height_cm'           => 'nullable|numeric|min:0',
        ]);

        $user = auth()->user();
        if ($user->medicalProfile) {
            $user->medicalProfile->update($data);
        } else {
            $user->medicalProfile()->create($data);
        }

        return redirect()->back()->with('success', 'Medical profile synchronized successfully.');
    }

    public function storeFamilyMember(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'relation'           => 'required|string|max:50',
            'blood_group'        => 'nullable|string|max:5',
            'allergies'          => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'contact_number'     => 'nullable|string|max:20',
        ]);

        auth()->user()->familyMembers()->create($data);
        return redirect()->back()->with('success', 'Family member added to your health ecosystem.');
    }

    public function destroyFamilyMember(FamilyMember $member)
    {
        abort_unless($member->user_id === auth()->id(), 403);
        $member->delete();
        return redirect()->back()->with('success', 'Family member removed.');
    }

    public function getFamilyMemberHealth(
        FamilyMember $member,
        \App\Services\AI\HealthRiskEngine $riskEngine,
        \App\Services\AI\ContextualRecommendationEngine $recommendationEngine
    ) {
        abort_unless($member->user_id === auth()->id(), 403);

        $member->load(['medicalProfile', 'medications', 'healthIndicators', 'aiRecommendations']);
        
        $risks = $riskEngine->calculateRisks($member);
        $latestRecommendation = $member->aiRecommendations()->latest()->first();

        return response()->json([
            'success' => true,
            'risks' => $risks,
            'recommendation' => $latestRecommendation,
            'medications' => $member->medications,
            'profile' => $member->medicalProfile,
            'reports_count' => $member->medicalReports()->count()
        ]);
    }

    // =========================================================================
    // AI MEDICAL REPORT PARSER
    // =========================================================================
    public function parseMedicalReport(Request $request)
    {
        $request->validate(['report' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        // In a real production system, this would upload to S3 and trigger an AWS Textract / Gemini API job.
        // For the sake of the ResQFlow prototype, we simulate a fast parsing delay and return mock intelligence.
        
        sleep(2); // Simulate processing time

        return response()->json([
            'success' => true,
            'data'    => [
                'blood_group'         => 'O+',
                'allergies'           => 'Penicillin, Peanuts',
                'chronic_conditions'  => 'Type 2 Diabetes, Mild Hypertension',
                'current_medications' => 'Metformin 500mg, Lisinopril 10mg',
            ],
            'message' => 'Medical intelligence extracted successfully.'
        ]);
    }
}
