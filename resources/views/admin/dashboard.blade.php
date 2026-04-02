@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
    ANALYTICS — Stat Cards
══════════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mt-1 mb-4">

    {{-- Total Visitors --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:#dbeafe;">
                    <i class="bi bi-people-fill" style="color:#1d4ed8;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalVisitors) }}</div>
                    <div class="stat-label">Total Visitors</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="bi bi-calendar-check" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($todayVisitors) }}</div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
        </div>
    </div>

    {{-- This Week --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="bi bi-calendar-week" style="color:#ca8a04;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($weekVisitors) }}</div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
        </div>
    </div>

    {{-- This Month --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon" style="background:#fce7f3;">
                    <i class="bi bi-calendar-month" style="color:#9d174d;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($monthVisitors) }}</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
    ANALYTICS — Charts (4 charts in a 2×2 grid)
══════════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Visitors Over Time (line chart, last 30 days) --}}
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-title">
                <i class="bi bi-graph-up me-1"></i> Visitors Over the Last 30 Days
            </div>
            <canvas id="visitorsTimeChart" style="max-height:220px;"></canvas>
        </div>
    </div>

    {{-- Gender Distribution --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-title">
                <i class="bi bi-gender-ambiguous me-1"></i> Gender Distribution
            </div>
            <canvas id="genderChart" style="max-height:220px;"></canvas>
        </div>
    </div>

    {{-- Transaction Type Distribution --}}
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="chart-title">
                <i class="bi bi-pie-chart me-1"></i> Transaction Type Distribution
            </div>
            <canvas id="transactionChart" style="max-height:260px;"></canvas>
        </div>
    </div>

    {{-- Top Municipalities --}}
    <div class="col-lg-7">
        <div class="chart-card h-100">
            <div class="chart-title">
                <i class="bi bi-geo-alt me-1"></i> Top Municipalities / Cities
            </div>
            <canvas id="municipalityChart" style="max-height:260px;"></canvas>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
    DATA TABLE — Filter Bar
══════════════════════════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm">

    <div class="filter-bar">
        <div class="row g-2 align-items-end">

            {{-- Search --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name, firm, address…"
                           value="{{ $search }}">
                </div>
            </div>

            {{-- Date From --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $dateFrom }}">
            </div>

            {{-- Date To --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $dateTo }}">
            </div>

            {{-- Gender Filter --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Gender</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">All Genders</option>
                    @foreach($genders as $g)
                        <option value="{{ $g }}" {{ $genderFilter === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Transaction Filter --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Transaction</label>
                <select name="transaction_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach($transactionTypes as $t)
                        <option value="{{ $t }}" {{ $transactionFilter === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Hidden sort fields (preserved across filter submissions) --}}
            <input type="hidden" name="sort_by"  value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">

            {{-- Buttons --}}
            <div class="col-lg-1 col-md-12 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm flex-fill"
                   title="Reset filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────
        Table Action Buttons
    ───────────────────────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
        <div>
            <span class="text-muted" style="font-size:0.85rem;">
                Showing <strong>{{ $logs->firstItem() ?? 0 }}</strong>–<strong>{{ $logs->lastItem() ?? 0 }}</strong>
                of <strong>{{ $logs->total() }}</strong> records
            </span>
        </div>
        <div class="d-flex gap-2">
            {{-- Export CSV: passes current filters --}}
            <a href="{{ route('admin.export.csv', array_filter([
                    'search'           => $search,
                    'date_from'        => $dateFrom,
                    'date_to'          => $dateTo,
                    'gender'           => $genderFilter,
                    'transaction_type' => $transactionFilter,
                ])) }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
            </a>

            {{-- Print: passes current filters --}}
            <a href="{{ route('admin.logs.print', array_filter([
                    'search'           => $search,
                    'date_from'        => $dateFrom,
                    'date_to'          => $dateTo,
                    'gender'           => $genderFilter,
                    'transaction_type' => $transactionFilter,
                ])) }}"
               class="btn btn-outline-secondary btn-sm" target="_blank">
                <i class="bi bi-printer me-1"></i>Print View
            </a>
        </div>
    </div>

</form>

{{-- ─────────────────────────────────────────────────────────────────────────
    Data Table
───────────────────────────────────────────────────────────────────────────── --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>
                        @include('admin.partials.sort-header', [
                            'label'   => 'Date Visited',
                            'column'  => 'date_visited',
                            'sortBy'  => $sortBy,
                            'sortDir' => $sortDir,
                        ])
                    </th>
                    <th>
                        @include('admin.partials.sort-header', [
                            'label'   => 'Name of Firm',
                            'column'  => 'firm_name',
                            'sortBy'  => $sortBy,
                            'sortDir' => $sortDir,
                        ])
                    </th>
                    <th>
                        @include('admin.partials.sort-header', [
                            'label'   => 'Name of Client',
                            'column'  => 'client_name',
                            'sortBy'  => $sortBy,
                            'sortDir' => $sortDir,
                        ])
                    </th>
                    <th>Gender</th>
                    <th>Details of Transaction</th>
                    <th>
                        @include('admin.partials.sort-header', [
                            'label'   => 'Address',
                            'column'  => 'address',
                            'sortBy'  => $sortBy,
                            'sortDir' => $sortDir,
                        ])
                    </th>
                    <th>Contact #</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        {{-- Date Visited --}}
                        <td style="white-space:nowrap;">
                            <div style="font-weight:500;">{{ $log->date_visited->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">{{ $log->date_visited->format('h:i A') }}</div>
                        </td>

                        {{-- Firm --}}
                        <td>{{ $log->firm_name }}</td>

                        {{-- Client --}}
                        <td style="font-weight:500;">{{ $log->client_name }}</td>

                        {{-- Gender --}}
                        <td>
                            @php
                                $genderColor = match($log->gender) {
                                    'Male'   => 'primary',
                                    'Female' => 'danger',
                                    default  => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $genderColor }}-subtle text-{{ $genderColor }}-emphasis border border-{{ $genderColor }}-subtle"
                                  style="font-size:0.75rem;">
                                {{ $log->gender }}
                            </span>
                        </td>

                        {{-- Transaction --}}
                        <td>
                            @php
                                $txColors = [
                                    'SETUP'        => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                                    'GIA'          => ['bg' => '#dcfce7', 'color' => '#166534'],
                                    'CEST'         => ['bg' => '#fef9c3', 'color' => '#854d0e'],
                                    'Scholarship'  => ['bg' => '#fce7f3', 'color' => '#9d174d'],
                                    'S&T Referrals'=> ['bg' => '#ede9fe', 'color' => '#5b21b6'],
                                    'Others'       => ['bg' => '#f3f4f6', 'color' => '#374151'],
                                ];
                                $txStyle = $txColors[$log->transaction_type] ?? $txColors['Others'];
                            @endphp
                            <span class="badge"
                                  style="background:{{ $txStyle['bg'] }}; color:{{ $txStyle['color'] }}; font-size:0.75rem; font-weight:500;">
                                {{ $log->transaction_type }}
                            </span>
                            @if($log->transaction_type === 'Others' && $log->transaction_other_details)
                                <div class="text-muted mt-1" style="font-size:0.78rem; max-width:160px;">
                                    {{ Str::limit($log->transaction_other_details, 50) }}
                                </div>
                            @endif
                        </td>

                        {{-- Address --}}
                        <td>{{ $log->address }}</td>

                        {{-- Contact --}}
                        <td style="white-space:nowrap;">{{ $log->contact_number }}</td>

                        {{-- Delete Action --}}
                        <td class="text-center">
                            <form method="POST"
                                  action="{{ route('admin.logs.destroy', $log) }}"
                                  onsubmit="return confirm('Delete this record for {{ addslashes($log->client_name) }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        title="Delete record">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2.5rem; display:block; margin-bottom:.5rem; opacity:.4;"></i>
                            No records found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div class="d-flex justify-content-center py-3 border-top">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection


@push('scripts')
{{-- Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    // ──────────────────────────────────────────────────────────────
    // Data injected from the controller (PHP → JS)
    // ──────────────────────────────────────────────────────────────
    const transactionData = @json($transactionDistribution);
    const genderData      = @json($genderDistribution);
    const timeData        = @json($visitorsOverTime);
    const municipalData   = @json($topMunicipalities);

    // Colour palettes
    const txColors  = ['#3b82f6','#22c55e','#eab308','#ec4899','#8b5cf6','#6b7280'];
    const genColors = ['#3b82f6','#ec4899','#94a3b8'];

    // ──────────────────────────────────────────────────────────────
    // 1. Visitors Over Time — Line Chart
    // ──────────────────────────────────────────────────────────────
    new Chart(document.getElementById('visitorsTimeChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(timeData),
            datasets: [{
                label: 'Visitors',
                data: Object.values(timeData),
                backgroundColor: 'rgba(59,130,246,0.18)',
                borderColor: '#3b82f6',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxTicksLimit: 10 } },
            },
        },
    });

    // ──────────────────────────────────────────────────────────────
    // 2. Gender Distribution — Doughnut Chart
    // ──────────────────────────────────────────────────────────────
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(genderData),
            datasets: [{
                data: Object.values(genderData),
                backgroundColor: genColors,
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } },
            },
        },
    });

    // ──────────────────────────────────────────────────────────────
    // 3. Transaction Type Distribution — Pie Chart
    // ──────────────────────────────────────────────────────────────
    new Chart(document.getElementById('transactionChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(transactionData),
            datasets: [{
                data: Object.values(transactionData),
                backgroundColor: txColors,
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } },
            },
        },
    });

    // ──────────────────────────────────────────────────────────────
    // 4. Top Municipalities — Horizontal Bar Chart
    // ──────────────────────────────────────────────────────────────
    new Chart(document.getElementById('municipalityChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(municipalData),
            datasets: [{
                label: 'Visitors',
                data: Object.values(municipalData),
                backgroundColor: 'rgba(139,92,246,0.75)',
                borderColor: '#7c3aed',
                borderWidth: 1,
                borderRadius: 4,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } },
            },
        },
    });
}());
</script>
@endpush
