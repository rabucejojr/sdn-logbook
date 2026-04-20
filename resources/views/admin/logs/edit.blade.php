@extends('layouts.admin')

@section('title', 'Edit Log Record')
@section('page-title', 'Edit Log Record')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-xl-8 col-lg-10">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="font-size:0.85rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Edit Record</li>
            </ol>
        </nav>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-card p-4">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--dost-blue);">
                        <i class="bi bi-pencil-square me-2"></i>Edit Log Record
                    </h5>
                    <div class="text-muted" style="font-size:0.82rem;">
                        Record #{{ $clientLog->id }} &nbsp;·&nbsp;
                        Created {{ $clientLog->created_at->format('M d, Y') }}
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>

            <form method="POST" action="{{ route('admin.logs.update', $clientLog) }}" novalidate>
                @csrf
                @method('PUT')

                {{-- ── Date Visited ── --}}
                <div class="mb-3">
                    <label for="date_visited" class="form-label fw-semibold">
                        Date &amp; Time Visited <span class="text-danger">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        id="date_visited"
                        name="date_visited"
                        class="form-control @error('date_visited') is-invalid @enderror"
                        value="{{ old('date_visited', $clientLog->date_visited->format('Y-m-d\TH:i')) }}"
                        required
                    >
                    @error('date_visited')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                <h6 class="fw-bold text-uppercase mb-3" style="font-size:0.78rem; letter-spacing:0.06em; color:#6b7280;">
                    <i class="bi bi-person me-1"></i>Client Information
                </h6>

                {{-- ── Name of Firm ── --}}
                <div class="mb-3">
                    <label for="firm_name" class="form-label">
                        Name of Firm / Organization <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="firm_name"
                        name="firm_name"
                        class="form-control @error('firm_name') is-invalid @enderror"
                        value="{{ old('firm_name', $clientLog->firm_name) }}"
                        maxlength="255"
                        required
                    >
                    @error('firm_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Name of Client ── --}}
                <div class="mb-3">
                    <label for="client_name" class="form-label">
                        Name of Client <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="client_name"
                        name="client_name"
                        class="form-control @error('client_name') is-invalid @enderror"
                        value="{{ old('client_name', $clientLog->client_name) }}"
                        maxlength="255"
                        required
                    >
                    @error('client_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Gender ── --}}
                <div class="mb-3">
                    <label for="gender" class="form-label">
                        Gender <span class="text-danger">*</span>
                    </label>
                    <select
                        id="gender"
                        name="gender"
                        class="form-select @error('gender') is-invalid @enderror"
                        required
                    >
                        @foreach(['Male', 'Female', 'Prefer not to say'] as $g)
                            <option value="{{ $g }}"
                                {{ old('gender', $clientLog->gender) === $g ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Details of Transaction ── --}}
                <div class="mb-3">
                    <label for="transaction_type" class="form-label">
                        Details of Transaction <span class="text-danger">*</span>
                    </label>
                    <select
                        id="transaction_type"
                        name="transaction_type"
                        class="form-select @error('transaction_type') is-invalid @enderror"
                        required
                    >
                        @foreach(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others'] as $type)
                            <option value="{{ $type }}"
                                {{ old('transaction_type', $clientLog->transaction_type) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('transaction_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Conditional: "Others" textarea ── --}}
                <div class="mb-3" id="other-details-wrapper" style="display:none;">
                    <label for="transaction_other_details" class="form-label">
                        Please specify <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="transaction_other_details"
                        name="transaction_other_details"
                        rows="3"
                        class="form-control @error('transaction_other_details') is-invalid @enderror"
                        placeholder="Briefly describe the nature of the transaction…"
                        maxlength="500"
                    >{{ old('transaction_other_details', $clientLog->transaction_other_details) }}</textarea>
                    <div class="form-text text-end">
                        <span id="char-count">0</span>/500 characters
                    </div>
                    @error('transaction_other_details')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Address ── --}}
                <div class="mb-3">
                    <label for="address" class="form-label">
                        Address <span class="text-danger">*</span>
                        <span class="text-muted fw-normal" style="font-size:0.82rem;">(Municipality / City)</span>
                    </label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control @error('address') is-invalid @enderror"
                        value="{{ old('address', $clientLog->address) }}"
                        maxlength="255"
                        required
                    >
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Contact Number ── --}}
                <div class="mb-3">
                    <label for="contact_number" class="form-label">
                        Contact Number <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input
                            type="tel"
                            id="contact_number"
                            name="contact_number"
                            class="form-control @error('contact_number') is-invalid @enderror"
                            value="{{ old('contact_number', $clientLog->contact_number) }}"
                            placeholder="09XXXXXXXXX"
                            maxlength="13"
                            required
                        >
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Philippine mobile format: 09XXXXXXXXX</div>
                </div>

                {{-- ── Email ── --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email Address
                        <span class="text-muted fw-normal" style="font-size:0.82rem;">(optional)</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $clientLog->email) }}"
                            placeholder="e.g., juan@example.com"
                            maxlength="255"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold text-uppercase mb-3" style="font-size:0.78rem; letter-spacing:0.06em; color:#6b7280;">
                    <i class="bi bi-lock-fill me-1"></i>Staff Entry
                </h6>

                {{-- ── Attended By ── --}}
                <div class="mb-3">
                    <label for="attended_by" class="form-label fw-semibold">
                        Attended by <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="attended_by"
                        name="attended_by"
                        class="form-control @error('attended_by') is-invalid @enderror"
                        value="{{ old('attended_by', $clientLog->attended_by) }}"
                        placeholder="Staff member's name"
                        maxlength="255"
                    >
                    @error('attended_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Remarks ── --}}
                <div class="mb-4">
                    <label for="remarks" class="form-label fw-semibold">
                        Remarks
                        <span class="text-muted fw-normal" style="font-size:0.82rem;">(optional)</span>
                    </label>
                    <textarea
                        id="remarks"
                        name="remarks"
                        rows="3"
                        class="form-control @error('remarks') is-invalid @enderror"
                        placeholder="Any notes about this transaction…"
                        maxlength="1000"
                    >{{ old('remarks', $clientLog->remarks) }}</textarea>
                    <div class="form-text text-end">
                        <span id="remarks-char-count">0</span>/1000 characters
                    </div>
                    @error('remarks')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Actions ── --}}
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy me-2"></i>Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Transaction type toggle ──────────────────────────────────────────────
    const typeSelect   = document.getElementById('transaction_type');
    const otherWrapper = document.getElementById('other-details-wrapper');
    const otherField   = document.getElementById('transaction_other_details');
    const charCount    = document.getElementById('char-count');

    function toggleOtherField() {
        const isOthers = typeSelect.value === 'Others';
        otherWrapper.style.display = isOthers ? 'block' : 'none';
        otherField.required = isOthers;
        if (!isOthers) {
            otherField.value = '';
            charCount.textContent = '0';
        }
    }

    function updateCharCount() {
        charCount.textContent = otherField.value.length;
    }

    typeSelect.addEventListener('change', toggleOtherField);
    otherField.addEventListener('input', updateCharCount);
    toggleOtherField();
    updateCharCount();

    // ── Remarks character counter ────────────────────────────────────────────
    const remarksField = document.getElementById('remarks');
    const remarksCount = document.getElementById('remarks-char-count');
    if (remarksField && remarksCount) {
        remarksCount.textContent = remarksField.value.length;
        remarksField.addEventListener('input', function () {
            remarksCount.textContent = this.value.length;
        });
    }
}());
</script>
@endpush
