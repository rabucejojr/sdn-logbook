<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLog;

class PendingController extends Controller
{
    public function index()
    {
        $pending = ClientLog::pending()->orderBy('created_at', 'asc')->get();

        return view('admin.pending.index', compact('pending'));
    }

    public function reject(ClientLog $clientLog)
    {
        $name = $clientLog->client_name_display;
        $clientLog->delete();

        return redirect()->route('admin.pending.index')
            ->with('success', 'Submission from "' . $name . '" has been rejected and removed.');
    }
}
