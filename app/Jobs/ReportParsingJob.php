<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\MedicalReport;
use App\Services\AIService;

class ReportParsingJob implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct(MedicalReport $report)
    {
        $this->report = $report;
    }

    public function handle(AIService $aiService): void
    {
        $this->report->update(['status' => 'processing']);

        try {
            // 1. Simulate OCR Extraction (In production, use Tesseract/AWS Textract)
            $rawText = "Sample extracted medical text from " . $this->report->file_name;
            
            // 2. AI Parsing & Structuring
            $parsedData = $aiService->parseReport($rawText);
            
            // 3. AI Health Intelligence Analysis
            $analysis = $aiService->analyzeHealthProfile($parsedData);

            // 4. Update Report
            $this->report->update([
                'parsed_data' => $parsedData,
                'ai_summary' => $analysis['summary'], // Store the text summary
                'status' => 'completed',
                'confidence_score' => $parsedData['confidence'] ?? 0.9
            ]);

            // Optional: Store analysis in a more structured way if needed
            // e.g., $this->report->user->updateMedicalProfile($parsedData);

        } catch (\Exception $e) {
            \Log::error('[AI] Report Parsing Failed: ' . $e->getMessage());
            $this->report->update(['status' => 'failed']);
        }
    }
}
