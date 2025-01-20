<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportUpdates(Request $request)
    {
        $request->validate([
            'type' => 'in:xlsx,csv', // Allow only xlsx and csv formats
        ]);

        try {
            $fileType = $request->get('type', 'xlsx');
            $fileName = 'updates_' . now()->format('Y-m-d_H-i-s') . '.' . $fileType;

            $exportFormat = match (strtolower($fileType)) {
                'csv' => \Maatwebsite\Excel\Excel::CSV,
                'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
                default => \Maatwebsite\Excel\Excel::XLSX,
            };

            // Store the file in the `storage/exports` directory
            $path = 'exports/' . $fileName;
            Excel::store(new ReportExport, $path, 'local', $exportFormat);

            // Log success or return a success response
            Log::info("Exported updates saved to: {$path}");
            return response()->json([
                'success' => true,
                'file' => $path,
                'message' => 'File exported successfully.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error exporting updates', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to export updates.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



}
