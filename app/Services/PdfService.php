<?php

namespace App\Services;

use ZanySoft\LaravelPDF\Facades\PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate PDF with standardized error handling and memory management
     *
     * @param string $view
     * @param array $data
     * @param string|null $filename
     * @param string $orientation
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public static function generate($view, $data, $filename = null, $orientation = 'portrait')
    {
        try {
            // Set execution limits for PDF generation
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');

            // Load PDF with data
            $pdf = PDF::loadView($view, $data);
            $pdf->setPaper('A4', $orientation);

            // Return download or stream based on filename
            if ($filename) {
                return $pdf->download($filename);
            }

            return $pdf->stream();

        } catch (\Exception $e) {
            Log::error("PDF generation failed for view: {$view} - " . $e->getMessage());
            throw new \Exception("Erreur lors de la génération du PDF: " . $e->getMessage());
        }
    }

    /**
     * Generate PDF for heavy reports (use queue)
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $orientation
     * @return bool
     */
    public static function generateQueued($view, $data, $filename, $orientation = 'portrait')
    {
        try {
            // Dispatch to queue for heavy processing
            \App\Jobs\DownloadPdf::dispatch($view, $data, $filename, $orientation);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to queue PDF generation: {$filename} - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up old PDF files
     *
     * @param int $hoursOld
     * @return int Number of files deleted
     */
    public static function cleanupOldFiles($hoursOld = 24)
    {
        $deletedCount = 0;
        $files = Storage::files('pdfs');

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            if (\Carbon\Carbon::createFromTimestamp($lastModified)->addHours($hoursOld)->isPast()) {
                Storage::delete($file);
                $deletedCount++;
            }
        }

        Log::info("Cleaned up {$deletedCount} old PDF files");
        return $deletedCount;
    }
}
