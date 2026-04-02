<?php

namespace App\Http\Controllers;

use App\Models\ClientLog;
use App\Http\Requests\StoreClientLogRequest;

class LogbookController extends Controller
{
    /**
     * Show the public client visit log form.
     */
    public function index()
    {
        return view('logbook.index');
    }

    /**
     * Validate and store a new client visit log entry.
     *
     * Security notes:
     *  - date_visited is ALWAYS set server-side via now() — never from user input.
     *  - transaction_other_details is explicitly nullified when type is not "Others",
     *    preventing hidden-field injection of unwanted data.
     *  - A one-time flash key guards the success page against direct access.
     */
    public function store(StoreClientLogRequest $request)
    {
        $validated = $request->validated();

        // BUG FIX: Always clear other_details unless the type is "Others".
        // Without this, an attacker could POST transaction_other_details
        // even when a non-Others type is selected, polluting the database.
        $otherDetails = $validated['transaction_type'] === 'Others'
            ? ($validated['transaction_other_details'] ?? null)
            : null;

        ClientLog::create([
            'date_visited'              => now(),
            'firm_name'                 => $validated['firm_name'],
            'client_name'               => $validated['client_name'],
            'gender'                    => $validated['gender'],
            'transaction_type'          => $validated['transaction_type'],
            'transaction_other_details' => $otherDetails,
            'address'                   => $validated['address'],
            'contact_number'            => $validated['contact_number'],
        ]);

        // Flash key so the success page knows it was reached via a real submission,
        // not by navigating to the URL directly.
        return redirect()->route('logbook.success')->with('just_submitted', true);
    }

    /**
     * Show the success confirmation page.
     * Redirects away if reached directly (no flash key present).
     */
    public function success()
    {
        if (! session('just_submitted')) {
            return redirect()->route('logbook.index');
        }

        return view('logbook.success');
    }
}
