<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportUpdates(Request $request)
    {
        $fileType = $request->get('type', 'xlsx'); // Default to XLSX
        $fileName = 'updates_' . now()->format('Y-m-d_H-i-s') . '.' . $fileType;

        $exportFormat = match (strtolower($fileType)) {
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            default => \Maatwebsite\Excel\Excel::XLSX,
        };

        return Excel::download(new ReportExport, $fileName, $exportFormat);
    }
}
