<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\MedicalReport;

class RuntimeAuditSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // Clear existing to ensure clean audit
        $user->familyMembers()->delete();
        $user->medicalReports()->delete();

        $member = $user->familyMembers()->create([
            'name' => 'Sarah Johnson',
            'relation' => 'Spouse',
            'blood_group' => 'A+',
            'contact_number' => '+1 555-0102'
        ]);

        $member->medicalProfile()->create([
            'user_id' => $user->id,
            'blood_group' => 'A+',
            'allergies' => 'Latex',
            'chronic_conditions' => 'Asthma'
        ]);

        // Newest Report
        $user->medicalReports()->create([
            'file_name' => 'Cardiac_Screening_LATEST.pdf',
            'file_path' => 'medical-reports/1/cardiac.pdf',
            'status' => 'completed',
            'confidence_score' => 0.98,
            'created_at' => now(),
        ]);

        // Older Report
        $user->medicalReports()->create([
            'file_name' => 'Blood_Work_OLD.png',
            'file_path' => 'medical-reports/1/blood.png',
            'status' => 'completed',
            'confidence_score' => 0.85,
            'created_at' => now()->subDays(10),
        ]);

        // Family Member Report
        $user->medicalReports()->create([
            'file_name' => 'Sarah_Health_Audit.pdf',
            'file_path' => 'medical-reports/1/sarah.pdf',
            'status' => 'completed',
            'confidence_score' => 0.92,
            'family_member_id' => $member->id,
            'created_at' => now()->subMinutes(30),
        ]);
    }
}
