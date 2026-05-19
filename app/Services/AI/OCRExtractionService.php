<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class OCRExtractionService
{
    protected GroqClient $groq;

    public function __construct(GroqClient $groq)
    {
        $this->groq = $groq;
    }

    /**
     * Extract text from a medical report file path.
     */
    public function extractText(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            return $this->extractFromImage($path);
        }

        if (strtolower($extension) === 'pdf') {
            return $this->extractFromPdf($path);
        }

        throw new \Exception('Unsupported file format for OCR extraction.');
    }

    protected function extractFromImage(string $path): string
    {
        Log::info('Initiating Vision-based OCR extraction from path');
        return "IMAGE_CONTENT_PLACEHOLDER: " . substr(base64_encode(file_get_contents($path)), 0, 500);
    }

    protected function extractFromPdf(string $path): string
    {
        Log::info('Initiating REAL PDF text extraction from path');
        
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF Parsing failed: ' . $e->getMessage());
            return "ERROR: Failed to extract text from PDF.";
        }
    }
}
