<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Stream a CSV export of client logs.
     * Applies the same filters as the dashboard table so exports match
     * what the admin is currently viewing.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $logs = ClientLog::query()
            ->search($request->input('search'))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->filterGender($request->input('gender'))
            ->filterTransaction($request->input('transaction_type'))
            ->orderBy('date_visited', 'desc')
            ->get();

        $filename = 'client_logs_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for proper Excel rendering
            fputs($handle, "\xEF\xBB\xBF");

            // Column headers
            fputcsv($handle, [
                'Date Visited',
                'Name of Firm',
                'Name of Client',
                'Gender',
                'Details of Transaction',
                'Address',
                'Contact Number',
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->date_visited->format('Y-m-d H:i:s'),
                    $log->firm_name,
                    $log->client_name,
                    $log->gender,
                    $log->transaction_display,
                    $log->address,
                    $log->contact_number,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
