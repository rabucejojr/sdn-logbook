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

                {{-- ── Email Address ── --}}
                <div class="mb-4">
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
                            value="{{ old('email') }}"
                            placeholder="e.g., juan@example.com"
                            maxlength="255"
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ── For Staff Use Only ── --}}
                <div class="card border-warning mb-4" style="background-color:#fffbeb;">
                    <div class="card-header d-flex align-items-center gap-2 py-2"
                         style="background-color:#fef3c7; border-bottom:1px solid #fcd34d;">
                        <i class="bi bi-lock-fill text-warning"></i>
                        <span class="fw-bold text-warning-emphasis" style="font-size:0.95rem; letter-spacing:0.03em;">
                            FOR STAFF USE ONLY
                        </span>
                    </div>
                    <div class="card-body pt-3 pb-2">

                        {{-- Attended By --}}
                        <div class="mb-3">
                            <label for="attended_by" class="form-label fw-semibold">
                                Assisted by <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="attended_by"
                                name="attended_by"
                                class="form-control @error('attended_by') is-invalid @enderror"
                                value="{{ old('attended_by') }}"
                                placeholder="Name / Position"
                                maxlength="255"
                                autocomplete="off"
                            >
                            @error('attended_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remarks --}}
                        <div class="mb-1">
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
                            >{{ old('remarks') }}</textarea>
                            <div class="form-text text-end">
                                <span id="remarks-char-count">0</span>/1000 characters
                            </div>
                            @error('remarks')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── Review & Submit ── --}}
                <div class="d-grid">
                    <button type="button" id="btn-review" class="btn btn-primary btn-lg">
                        <i class="bi bi-eye me-2"></i>Review & Submit
                    </button>
                </div>

            </form>
        </div>

        {{-- ── Review Modal ── --}}
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header" style="background-color:var(--dost-blue); color:#fff;">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="reviewModalLabel">
                            <i class="bi bi-clipboard-check"></i> Review Your Entry
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 py-3">
                        <p class="text-muted mb-3" style="font-size:0.9rem;">
                            Please review the information below before confirming. Click <strong>Go back &amp; edit</strong> to make changes.
                        </p>

                        {{-- Client Information --}}
                        <h6 class="fw-bold text-uppercase mb-2" style="font-size:0.78rem; letter-spacing:0.06em; color:#6b7280;">
                            <i class="bi bi-person me-1"></i>Client Information
                        </h6>
                        <table class="table table-sm table-bordered mb-4" style="font-size:0.92rem;">
                            <tbody>
                                <tr>
                                    <th class="table-light" style="width:38%;">Name of Firm / Organization</th>
                                    <td id="rv-firm_name" class="fw-semibold"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Name of Client</th>
                                    <td id="rv-client_name" class="fw-semibold"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Gender</th>
                                    <td id="rv-gender"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Details of Transaction</th>
                                    <td id="rv-transaction"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Address</th>
                                    <td id="rv-address"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Contact Number</th>
                                    <td id="rv-contact_number"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Email Address</th>
                                    <td id="rv-email"></td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Staff Information --}}
                        <h6 class="fw-bold text-uppercase mb-2" style="font-size:0.78rem; letter-spacing:0.06em; color:#6b7280;">
                            <i class="bi bi-lock-fill me-1"></i>Staff Entry
                        </h6>
                        <table class="table table-sm table-bordered mb-0" style="font-size:0.92rem;">
                            <tbody>
                                <tr>
                                    <th class="table-light" style="width:38%;">Attended by</th>
                                    <td id="rv-attended_by" class="fw-semibold"></td>
                                </tr>
                                <tr>
                                    <th class="table-light">Remarks</th>
                                    <td id="rv-remarks"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-pencil me-1"></i>Go back &amp; edit
                        </button>
                        <button type="button" id="btn-confirm-submit" class="btn btn-success btn-lg px-4">
                            <i class="bi bi-send me-2"></i>Confirm &amp; Submit
                        </button>
                    </div>

                </div>
            </div>
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

    // ── Review modal ─────────────────────────────────────────────────────────
    const form           = document.querySelector('form[action="{{ route('logbook.store') }}"]');
    const btnReview      = document.getElementById('btn-review');
    const btnConfirm     = document.getElementById('btn-confirm-submit');
    const reviewModalEl  = document.getElementById('reviewModal');
    const reviewModal    = new bootstrap.Modal(reviewModalEl);

    // Helper: return value or a placeholder when the field is blank
    function val(id) {
        const el = document.getElementById(id);
        if (!el) return '';
        return el.value.trim() || '';
    }

    function blank(str) { return str === ''; }

    function setCell(id, text, isRequired) {
        const cell = document.getElementById(id);
        if (!cell) return;
        if (blank(text) && isRequired) {
            cell.innerHTML = '<span class="text-danger fst-italic">— not provided —</span>';
        } else if (blank(text)) {
            cell.innerHTML = '<span class="text-muted fst-italic">— none —</span>';
        } else {
            cell.textContent = text;
        }
    }

    function populateReview() {
        setCell('rv-firm_name',    val('firm_name'),    true);
        setCell('rv-client_name',  val('client_name'),  true);
        setCell('rv-gender',       val('gender'),       true);
        setCell('rv-address',      val('address'),      true);
        setCell('rv-contact_number', val('contact_number'), true);
        setCell('rv-email',        val('email'),         false);
        setCell('rv-attended_by',  val('attended_by'),  true);
        setCell('rv-remarks',      val('remarks'),      false);

        // Transaction — combine type + other details when "Others"
        const txType    = val('transaction_type');
        const txOther   = val('transaction_other_details');
        let txDisplay   = txType;
        if (txType === 'Others' && txOther) {
            txDisplay = 'Others: ' + txOther;
        }
        setCell('rv-transaction', txDisplay, true);
    }

    btnReview.addEventListener('click', function () {
        populateReview();
        reviewModal.show();
    });

    // Confirm button actually submits the form
    btnConfirm.addEventListener('click', function () {
        // Disable to prevent double-click
        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting…';
        form.submit();
    });

}());
</script>
@endpush
