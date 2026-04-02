<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Visit Logbook — DOST Surigao del Norte</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #111;
            background: #fff;
            padding: 20mm 15mm;
        }

        /* ── Header ── */
        .print-header {
            text-align: center;
            border-bottom: 3px double #003a8c;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .print-header .republic {
            font-size: 8.5pt;
            letter-spacing: 0.05em;
            color: #555;
            margin-bottom: 2px;
        }

        .print-header .agency {
            font-size: 14pt;
            font-weight: 700;
            color: #003a8c;
        }

        .print-header .province {
            font-size: 11pt;
            font-weight: 600;
            color: #003a8c;
        }

        .print-header .form-title {
            font-size: 12pt;
            font-weight: 700;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .print-header .date-generated {
            font-size: 9pt;
            color: #555;
            margin-top: 4px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        thead th {
            background-color: #003a8c;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: 600;
            border: 1px solid #002a6a;
        }

        tbody td {
            padding: 5px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        tbody tr:nth-child(even) { background-color: #f0f4ff; }

        /* ── Footer ── */
        .print-footer {
            margin-top: 20px;
            font-size: 9pt;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
        }

        /* ── Print button (hidden in actual print) ── */
        .no-print {
            margin-bottom: 16px;
        }

        .btn-print {
            background: #003a8c;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 11pt;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-close-tab {
            background: #6b7280;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 11pt;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 8px;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 10mm; }
            @page { margin: 10mm 12mm; size: landscape; }
        }
    </style>
</head>
<body>

    {{-- Print/Close buttons (hidden on print) --}}
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">&#128438; Print</button>
        <button class="btn-close-tab" onclick="window.close()">&#x2715; Close</button>
    </div>

    {{-- Document Header --}}
    <div class="print-header">
        <div class="republic">Republic of the Philippines</div>
        <div class="agency">Department of Science and Technology</div>
        <div class="province">Surigao del Norte Provincial Office</div>
        <div class="form-title">Client Visit Logbook</div>
        <div class="date-generated">
            Generated: {{ now()->format('F d, Y h:i A') }}
            &nbsp;&bull;&nbsp;
            Total Records: <strong>{{ $logs->count() }}</strong>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date Visited</th>
                <th>Name of Firm</th>
                <th>Name of Client</th>
                <th>Gender</th>
                <th>Details of Transaction</th>
                <th>Address</th>
                <th>Contact #</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $log->date_visited->format('M d, Y H:i') }}</td>
                    <td>{{ $log->firm_name }}</td>
                    <td>{{ $log->client_name }}</td>
                    <td>{{ $log->gender }}</td>
                    <td>{{ $log->transaction_display }}</td>
                    <td>{{ $log->address }}</td>
                    <td style="white-space:nowrap;">{{ $log->contact_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px; color:#888;">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Document Footer --}}
    <div class="print-footer">
        <span>DOST Surigao del Norte &mdash; Client Visit Logbook System</span>
        <span>Printed on: {{ now()->format('F d, Y h:i A') }}</span>
    </div>

</body>
</html>
