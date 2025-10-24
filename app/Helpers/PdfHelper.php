<?php

namespace App\Helpers;

class PdfHelper
{
    /**
     * Clean and normalize HTML content for PDF generation
     *
     * @param string $html
     * @return string
     */
    public static function cleanHtml(string $html): string
    {
        // 1) Remove UTF-8 BOM if present
        $html = preg_replace('/^\x{FEFF}/u', '', $html);
        
        // 2) Normalize line endings
        $html = str_replace(["\r\n", "\r"], "\n", $html);
        
        // 3) Remove embedded null bytes
        $html = str_replace("\0", '', $html);
        
        // 4) Remove non-printable control chars except newline, tab, carriage return
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html);
        
        // 5) Force to UTF-8 and drop invalid sequences
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        $html = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $html);
        
        // 6) Ensure proper HTML structure
        if (!preg_match('/<\s*!DOCTYPE\s+html\s*>/i', $html)) {
            $html = "<!DOCTYPE html>\n" . $html;
        }
        
        // 7) Ensure html and body tags exist
        if (!preg_match('/<\s*html[^>]*>/i', $html)) {
            $html = "<html>\n<head>\n<meta charset=\"UTF-8\">\n</head>\n<body>\n" . $html . "\n</body>\n</html>";
        } elseif (!preg_match('/<\s*head[^>]*>/i', $html)) {
            $html = preg_replace('/(<html[^>]*>)/i', "$1\n<head>\n<meta charset=\"UTF-8\">\n</head>", $html);
        } elseif (!preg_match('/<\s*meta[^>]*charset\s*=[^>]*>/i', $html)) {
            $html = preg_replace('/(<head[^>]*>)/i', "$1\n<meta charset=\"UTF-8\">", $html);
        }
        
        // 8) Convert special characters to HTML entities
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
        return $html;
    }
    
    /**
     * Normalize HTML so mPDF (or similar libs) won't reject it for invalid UTF-8 or BOM
     *
     * @param  string  $html
     * @return string
     */
    public static function normalizeHtmlForPdf(string $html): string
    {
        // 1) Remove UTF-8 BOM if present
        $html = preg_replace('/^\x{FEFF}/u', '', $html);

        // 2) Remove any bytes or whitespace before DOCTYPE
        $html = preg_replace('/^\s*/u', '', $html);

        // 3) Normalize line endings
        $html = str_replace(["\r\n", "\r"], "\n", $html);

        // 4) Remove embedded null bytes
        $html = str_replace("\0", '', $html);

        // 5) Remove non-printable control chars except newline and tab
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html);

        // 6) Force to UTF-8 and remove invalid sequences
        $html = @mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        $html = @iconv('UTF-8', 'UTF-8//IGNORE', $html) ?: $html;

        // 7) Ensure a proper DOCTYPE at the top to avoid Quirks Mode in browsers and rendering engines
        if (! preg_match('/^\s*<!DOCTYPE\s+html/i', $html)) {
            $html = "<!DOCTYPE html>\n" . $html;
        }

        // 8) Final trim
        return ltrim($html);
    }
    
    /**
     * Generate PDF from HTML with proper encoding
     *
     * @param string $html
     * @param string $filename
     * @param string $orientation ('portrait' or 'landscape')
     * @param string $paperSize (e.g., 'A4', 'letter')
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generatePdf(string $html, string $filename, string $orientation = 'portrait', string $paperSize = 'A4')
    {
        $html = self::cleanHtml($html);
        
        // Ensure the filename has .pdf extension
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'pdf') {
            $filename .= '.pdf';
        }
        
        // Generate PDF with proper options
        $pdf = \PDF::loadHTML($html)
            ->setPaper($paperSize, $orientation)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('defaultFont', 'Arial')
            ->setOption('dpi', 96)
            ->setOption('defaultEncoding', 'UTF-8');
        
        // Set proper headers
        return $pdf->stream($filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=0'
        ]);
    }
}
