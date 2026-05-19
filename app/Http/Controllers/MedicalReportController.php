<?php

namespace App\Http\Controllers;

use App\Models\MedicalReport;
use App\Jobs\ReportParsingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalReportController extends Controller
{
    public function index()
    {
        $reports = auth()->user()->medicalReports()->latest()->get();
        return view('medical.reports.index', compact('reports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
            'family_member_id' => 'nullable|exists:family_members,id',
        ]);

        $file = $request->file('report');
        $ownerId = auth()->id();
        $familyMemberId = $request->family_member_id;

        // Use private storage for sensitive medical data
        $path = $file->store('medical-reports/' . $ownerId); 

        $report = MedicalReport::create([
            'user_id' => $ownerId,
            'family_member_id' => $familyMemberId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // Dispatch Hardened AI Analysis Job
        \App\Jobs\AnalyzeMedicalReportJob::dispatch($report);

        return redirect()->back()->with('success', 'Report uploaded securely. ResQFlow AI is extracting intelligence.');
    }

    public function show(MedicalReport $report)
    {
        abort_unless($report->user_id === auth()->id(), 403);
        return view('medical.reports.show', compact('report'));
    }

    public function download(MedicalReport $report)
    {
        abort_unless($report->user_id === auth()->id(), 403);
        
        if (!Storage::exists($report->file_path)) {
            abort(404, 'Report file not found.');
        }

        return Storage::download($report->file_path, $report->file_name);
    }

    public function preview(MedicalReport $report)
    {
        abort_unless($report->user_id === auth()->id(), 403);

        if (!Storage::exists($report->file_path)) {
            abort(404, 'Report file not found.');
        }

        $content = Storage::get($report->file_path);
        $mime = Storage::mimeType($report->file_path);

        return response($content)->header('Content-Type', $mime);
    }
}
