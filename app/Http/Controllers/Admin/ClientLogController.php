<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLog;
use Illuminate\Http\Request;

class ClientLogController extends Controller
{
    /**
     * Delete a single log record.
     */
    public function destroy(ClientLog $clientLog)
    {
        $clientLog->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Record deleted successfully.');
    }

    /**
     * Print-friendly view of filtered log records.
     * Applies the same filters as the dashboard table.
     */
    public function printView(Request $request)
    {
        $logs = ClientLog::query()
            ->search($request->input('search'))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->filterGender($request->input('gender'))
            ->filterTransaction($request->input('transaction_type'))
            ->orderBy('date_visited', 'desc')
            ->get();

        return view('admin.logs.print', compact('logs'));
    }
}
