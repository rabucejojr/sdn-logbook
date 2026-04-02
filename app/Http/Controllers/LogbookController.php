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
     * date_visited is set server-side — never from user input.
     */
    public function store(StoreClientLogRequest $request)
    {
        $validated = $request->validated();

        ClientLog::create([
            'date_visited'              => now(),
            'firm_name'                 => $validated['firm_name'],
            'client_name'               => $validated['client_name'],
            'gender'                    => $validated['gender'],
            'transaction_type'          => $validated['transaction_type'],
            'transaction_other_details' => $validated['transaction_other_details'] ?? null,
            'address'                   => $validated['address'],
            'contact_number'            => $validated['contact_number'],
        ]);

        return redirect()->route('logbook.success');
    }

    /**
     * Show the success confirmation page after submission.
     */
    public function success()
    {
        return view('logbook.success');
    }
}
