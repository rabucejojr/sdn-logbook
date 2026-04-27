<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLog;
use App\Http\Requests\UpdateClientLogRequest;
use Illuminate\Http\Request;

class ClientLogController extends Controller
{
    /**
     * Show the edit form for a single log record.
     */
    public function edit(ClientLog $clientLog)
    {
        return view('admin.logs.edit', compact('clientLog'));
    }

    /**
     * Validate and persist updates to a single log record.
     * When _approve=1 is submitted, the record is also marked approved.
     */
    public function update(UpdateClientLogRequest $request, ClientLog $clientLog)
    {
        $validated = $request->validated();

        $otherDetails = in_array('Others', (array) ($validated['transaction_type'] ?? []))
            ? ($validated['transaction_other_details'] ?? null)
            : null;

        $isApproving = $request->input('_approve') === '1';

        $clientLog->update([
            'date_visited'              => $validated['date_visited'],
            'firm_name'                 => $validated['firm_name'],
            'client_name'               => $validated['client_name'],
            'gender'                    => $validated['gender'],
            'transaction_type'          => $validated['transaction_type'],
            'transaction_other_details' => $otherDetails,
            'address'                   => $validated['address'],
            'contact_number'            => $validated['contact_number'],
            'email'                     => $validated['email'] ?? null,
            'attended_by'               => $validated['attended_by'],
            'remarks'                   => $validated['remarks'] ?? null,
            'status'                    => $isApproving ? 'approved' : $clientLog->status,
        ]);

        if ($isApproving) {
            return redirect()->route('admin.pending.index')
                ->with('success', 'Record for "' . $clientLog->client_name_display . '" has been approved.');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Record for "' . $clientLog->client_name_display . '" updated successfully.');
    }

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
        $logs = ClientLog::approved()
            ->search($request->input('search'))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->filterGender($request->input('gender'))
            ->filterTransaction($request->input('transaction_type'))
            ->orderBy('date_visited', 'desc')
            ->get();

        return view('admin.logs.print', compact('logs'));
    }
}
