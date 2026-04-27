@extends('layouts.admin')

@section('title', 'Pending Approvals')
@section('page-title', 'Pending Approvals')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 mt-1">
    <div>
        <h5 class="fw-bold mb-0" style="color:var(--dost-blue);">
            <i class="bi bi-hourglass-split me-2"></i>Pending Client Submissions
        </h5>
        <div class="text-muted" style="font-size:0.82rem;">
            {{ $pending->count() }} submission(s) awaiting review and approval
        </div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
    </a>
</div>

@if($pending->isEmpty())
    <div class="table-card p-5 text-center">
        <i class="bi bi-check-circle-fill" style="font-size:3rem; color:#22c55e; display:block; margin-bottom:1rem;"></i>
        <h5 class="fw-semibold mb-1">All caught up!</h5>
        <p class="text-muted mb-0">There are no pending client submissions to review.</p>
    </div>
@else
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Submitted</th>
                        <th>Firm</th>
                        <th>Client(s)</th>
                        <th>Gender</th>
                        <th>Transaction</th>
                        <th>Address</th>
                        <th>Contact #</th>
                        <th>Email</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $log)
                        <tr>
                            {{-- Date Submitted --}}
                            <td style="white-space:nowrap;">
                                <div style="font-weight:500;">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-muted" style="font-size:0.78rem;">{{ $log->created_at->format('h:i A') }}</div>
                            </td>

                            {{-- Firm --}}
                            <td>{{ $log->firm_name }}</td>

                            {{-- Client(s) --}}
                            <td style="font-weight:500;">{{ $log->client_name_display }}</td>

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
                                        'SETUP'         => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                                        'GIA'           => ['bg' => '#dcfce7', 'color' => '#166534'],
                                        'CEST'          => ['bg' => '#fef9c3', 'color' => '#854d0e'],
                                        'Scholarship'   => ['bg' => '#fce7f3', 'color' => '#9d174d'],
                                        'S&T Referrals' => ['bg' => '#ede9fe', 'color' => '#5b21b6'],
                                        'Others'        => ['bg' => '#f3f4f6', 'color' => '#374151'],
                                    ];
                                @endphp
                                @foreach((array) $log->transaction_type as $txType)
                                    @php $txStyle = $txColors[$txType] ?? $txColors['Others']; @endphp
                                    <span class="badge me-1"
                                          style="background:{{ $txStyle['bg'] }}; color:{{ $txStyle['color'] }}; font-size:0.75rem; font-weight:500;">
                                        {{ $txType }}
                                    </span>
                                @endforeach
                                @if(in_array('Others', (array) $log->transaction_type) && $log->transaction_other_details)
                                    <div class="text-muted mt-1" style="font-size:0.78rem; max-width:160px;">
                                        {{ Str::limit($log->transaction_other_details, 40) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Address --}}
                            <td>{{ $log->address }}</td>

                            {{-- Contact --}}
                            <td style="white-space:nowrap;">{{ $log->contact_number }}</td>

                            {{-- Email --}}
                            <td>
                                @if($log->email)
                                    <a href="mailto:{{ $log->email }}" class="text-decoration-none"
                                       style="font-size:0.88rem;">{{ $log->email }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center" style="white-space:nowrap;">
                                {{-- Review & Approve → edit form --}}
                                <a href="{{ route('admin.logs.edit', $log) }}"
                                   class="btn btn-success btn-sm me-1"
                                   title="Review and approve this submission">
                                    <i class="bi bi-check-circle me-1"></i>Review
                                </a>
                                {{-- Reject → delete --}}
                                <form method="POST"
                                      action="{{ route('admin.pending.reject', $log) }}"
                                      class="d-inline"
                                      data-client-name="{{ $log->client_name_display }}"
                                      onsubmit="return confirm('Reject submission from \'' + this.dataset.clientName + '\'?\nThis will permanently delete the record.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            title="Reject and delete this submission">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
