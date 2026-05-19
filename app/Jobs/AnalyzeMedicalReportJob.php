<?php

namespace App\Jobs;

use App\Models\MedicalReport;
use App\Services\AI\MedicalAnalysisService;
use App\Services\AI\OCRExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnalyzeMedicalReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(protected MedicalReport $report) {}

    public function handle(MedicalAnalysisService $analysisService, OCRExtractionService $ocrService): void
    {
        try {
            $this->report->update(['status' => 'processing']);

            // 1. Text Extraction (OCR)
            $fullPath = storage_path('app/private/' . $this->report->file_path);
            
            if (!file_exists($fullPath)) {
                 // Fallback for disk mapping
                 $fullPath = storage_path('app/' . $this->report->file_path);
            }

            $extractedText = $ocrService->extractText($fullPath);

            // 2. Intelligence Analysis
            $analysis = $analysisService->analyzeReport($this->report, $extractedText);

            // 3. Update Report
            $this->report->update([
                'status' => 'completed',
                'parsed_data' => $analysis,
                'confidence_score' => $analysis['confidence_score'] ?? 0.0,
                'ai_summary' => "Intelligence extracted. Vitals and medications updated."
            ]);

            Log::info("Medical Report #{$this->report->id} successfully analyzed.");

        } catch (\Exception $e) {
            $this->report->update(['status' => 'failed', 'ai_summary' => 'Analysis failed: ' . $e->getMessage()]);
            Log::error("Analysis Job failed for Report #{$this->report->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
