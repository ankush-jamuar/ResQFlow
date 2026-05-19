<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    public function index()
    {
        $members = auth()->user()->familyMembers;
        return view('family.index', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        auth()->user()->familyMembers()->create($validated);

        return redirect()->back()->with('success', 'Family member added successfully.');
    }

    public function destroy(FamilyMember $member)
    {
        $this->authorize('delete', $member);
        $member->delete();

        return redirect()->back()->with('success', 'Family member removed.');
    }
}
