<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ZanySoft\LaravelPDF\Facades\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $view;
    protected $data;
    protected $filename;
    protected $orientation;

    /**
     * Create a new job instance.
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $orientation
     * @return void
     */
    public function __construct($view, $data, $filename, $orientation = 'portrait')
    {
        $this->view = $view;
        $this->data = $data;
        $this->filename = $filename;
        $this->orientation = $orientation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Set execution limits for heavy PDF processing
            ini_set('max_execution_time', 600); // 10 minutes for heavy reports
            ini_set('memory_limit', '1024M'); // 1GB for large datasets

            // Generate PDF
            $pdf = PDF::loadView($this->view, $this->data);
            $pdf->setPaper('A4', $this->orientation);

            // Ensure pdfs directory exists
            Storage::makeDirectory('pdfs');

            // Save PDF to storage
            Storage::put("pdfs/{$this->filename}", $pdf->output());

            // Log success
            Log::info("PDF generated successfully: {$this->filename}");

        } catch (\Exception $e) {
            Log::error("PDF generation failed: {$this->filename} - " . $e->getMessage());
            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle job failure
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error("PDF job failed permanently: {$this->filename} - " . $exception->getMessage());
        // Could send notification to admin here
    }
}
