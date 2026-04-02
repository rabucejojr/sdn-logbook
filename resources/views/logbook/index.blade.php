@extends('layouts.public')

@section('title', 'Client Visit Log')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        {{-- Page heading --}}
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1" style="color: var(--dost-blue);">Client Visit Logbook</h2>
            <p class="text-muted mb-0">Please fill out the form below to record your visit. All fields are required unless stated otherwise.</p>
        </div>

        {{-- Validation errors summary --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1 shrink-0"></i>
                    <div>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Logbook Form --}}
        <div class="content-card">
            <form method="POST" action="{{ route('logbook.store') }}" novalidate>
                @csrf

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
                        value="{{ old('firm_name') }}"
                        placeholder="e.g., ABC Corporation"
                        required
                        autocomplete="organization"
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
                        value="{{ old('client_name') }}"
                        placeholder="e.g., Juan dela Cruz"
                        required
                        autocomplete="name"
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
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                        @foreach(['Male', 'Female', 'Prefer not to say'] as $g)
                            <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
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
                        <option value="" disabled {{ old('transaction_type') ? '' : 'selected' }}>Select transaction type</option>
                        @foreach(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others'] as $type)
                            <option value="{{ $type }}" {{ old('transaction_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('transaction_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Conditional: "Others" specification textarea ── --}}
                <div class="mb-3" id="other-details-wrapper" style="display: none;">
                    <label for="transaction_other_details" class="form-label">
                        Please specify <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="transaction_other_details"
                        name="transaction_other_details"
                        rows="3"
                        class="form-control @error('transaction_other_details') is-invalid @enderror"
                        placeholder="Briefly describe the nature of your transaction…"
                        maxlength="500"
                    >{{ old('transaction_other_details') }}</textarea>
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
                        value="{{ old('address') }}"
                        placeholder="e.g., Surigao City, Surigao del Norte"
                        required
                    >
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Contact Number ── --}}
                <div class="mb-4">
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
                            value="{{ old('contact_number') }}"
                            placeholder="09XXXXXXXXX"
                            required
                            maxlength="13"
                        >
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Philippine mobile format: 09XXXXXXXXX</div>
                </div>

                {{-- ── Submit ── --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-send me-2"></i>Submit Visit Log
                    </button>
                </div>

            </form>
        </div>

        {{-- Admin login link --}}
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-muted" style="font-size:0.82rem; text-decoration:none;">
                <i class="bi bi-shield-lock me-1"></i>Admin Login
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const typeSelect   = document.getElementById('transaction_type');
    const otherWrapper = document.getElementById('other-details-wrapper');
    const otherField   = document.getElementById('transaction_other_details');
    const charCount    = document.getElementById('char-count');

    // Show/hide the "Others" textarea based on the selected transaction type
    function toggleOtherField() {
        const isOthers = typeSelect.value === 'Others';
        otherWrapper.style.display = isOthers ? 'block' : 'none';
        otherField.required = isOthers;

        if (!isOthers) {
            // Clear the field when hidden so stale data is not submitted
            otherField.value = '';
            charCount.textContent = '0';
        }
    }

    // Character counter for the textarea
    function updateCharCount() {
        charCount.textContent = otherField.value.length;
    }

    typeSelect.addEventListener('change', toggleOtherField);
    otherField.addEventListener('input', updateCharCount);

    // On page load: restore state if the form was returned with old() values
    // (e.g., after a validation failure)
    toggleOtherField();
    updateCharCount();
}());
</script>
@endpush
