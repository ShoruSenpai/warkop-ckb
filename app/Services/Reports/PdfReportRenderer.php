<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\Log;
use RuntimeException;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Facades\Pdf;
use Throwable;

class PdfReportRenderer
{
    public function download(
        string $view,
        array $data,
        string $filenamePrefix,
        bool $landscape = false,
    ) {
        $reportTitle = $data['reportTitle'];
        $generatedAt = now();
        $filename = $filenamePrefix.'-'.$generatedAt->format('d-m-Y-H-i').'.pdf';

        try {
            $pdf = Pdf::view($view, [
                ...$data,
                'generatedAt' => $generatedAt,
                'chartJs' => $this->readAsset(base_path('node_modules/chart.js/dist/chart.umd.js')),
                'chartRenderer' => $this->readAsset(resource_path('js/pdf/report-chart.js')),
                'pdfStyles' => $this->readAsset(resource_path('css/pdf.css')),
            ])
                ->format(Format::A4)
                ->margins(22, 12, 20, 12, Unit::Millimeter)
                ->headerView('pdf.reports.partials.header', compact('reportTitle', 'generatedAt'))
                ->footerView('pdf.reports.partials.footer', compact('reportTitle', 'generatedAt'))
                ->waitUntilReady('window.pdfReady === true', timeout: 15000);

            if ($landscape) {
                $pdf->landscape();
            }

            return $pdf->download($filename);
        } catch (Throwable $exception) {
            Log::error('Report PDF generation failed.', [
                'report' => $reportTitle,
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function readAsset(string $path): string
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("Required PDF asset is unavailable: {$path}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Required PDF asset could not be read: {$path}");
        }

        return $contents;
    }
}
