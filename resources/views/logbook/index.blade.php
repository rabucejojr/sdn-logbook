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

                {{-- ── Name of Client(s) ── --}}
                <div class="mb-3">
                    <label class="form-label">
                        Name of Client <span class="text-danger">*</span>
                        <span class="text-muted fw-normal" style="font-size:0.82rem;">(add more if multiple clients)</span>
                    </label>

                    <div id="client-names-wrapper">
                        {{-- First client name row (always present) --}}
                        <div class="client-name-row d-flex gap-2 mb-2 align-items-start">
                            <input
                                type="text"
                                name="client_name[]"
                                class="form-control @error('client_name.0') is-invalid @enderror"
                                value="{{ old('client_name.0', is_array(old('client_name')) ? old('client_name')[0] ?? '' : '') }}"
                                placeholder="e.g., Juan dela Cruz"
                                required
                                autocomplete="name"
                            >
                            <button type="button" class="btn btn-outline-danger btn-remove-client d-none"
                                    title="Remove this client" style="min-width:38px;">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                        </div>

                        {{-- Additional client rows from old() on validation failure --}}
                        @if(is_array(old('client_name')))
                            @foreach(old('client_name') as $i => $name)
                                @if($i > 0)
                                <div class="client-name-row d-flex gap-2 mb-2 align-items-start">
                                    <input
                                        type="text"
                                        name="client_name[]"
                                        class="form-control"
                                        value="{{ $name }}"
                                        placeholder="e.g., Maria Santos"
                                        required
                                        autocomplete="name"
                                    >
                                    <button type="button" class="btn btn-outline-danger btn-remove-client"
                                            title="Remove this client" style="min-width:38px;">
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <button type="button" id="btn-add-client" class="btn btn-outline-primary btn-sm mt-1">
                        <i class="bi bi-plus-lg me-1"></i>Add another client
                    </button>

                    @error('client_name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    @error('client_name.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
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

                {{-- ── Details of Transaction (multiple) ── --}}
                <div class="mb-3">
                    <label class="form-label">
                        Details of Transaction <span class="text-danger">*</span>
                        <span class="text-muted fw-normal" style="font-size:0.82rem;">(select all that apply)</span>
                    </label>

                    <div class="border rounded p-3 @error('transaction_type') border-danger @enderror"
                         style="background:#f8fafc;">
                        <div class="row g-2">
                            @foreach(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others'] as $type)
                            <div class="col-6 col-sm-4">
                                <div class="form-check">
                                    <input
                                        class="form-check-input transaction-checkbox"
                                        type="checkbox"
                                        name="transaction_type[]"
                                        id="tx_{{ Str::slug($type) }}"
                                        value="{{ $type }}"
                                        {{ is_array(old('transaction_type')) && in_array($type, old('transaction_type')) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="tx_{{ Str::slug($type) }}">
                                        {{ $type }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @error('transaction_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── Conditional: "Others" specification textarea ── --}}
                <div class="mb-3" id="other-details-wrapper" style="display: none;">
                    <label for="transaction_other_details" class="form-label">
                        Please specify (Others) <span class="text-danger">*</span>
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
                                    <th class="table-light">Name of Client(s)</th>
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

    // ── Dynamic client name rows ─────────────────────────────────────────────
    const namesWrapper  = document.getElementById('client-names-wrapper');
    const btnAddClient  = document.getElementById('btn-add-client');

    function refreshRemoveButtons() {
        const rows = namesWrapper.querySelectorAll('.client-name-row');
        rows.forEach(function (row) {
            const btn = row.querySelector('.btn-remove-client');
            if (btn) btn.classList.toggle('d-none', rows.length === 1);
        });
    }

    function addClientRow() {
        const row = document.createElement('div');
        row.className = 'client-name-row d-flex gap-2 mb-2 align-items-start';
        row.innerHTML =
            '<input type="text" name="client_name[]" class="form-control"' +
            ' placeholder="e.g., Maria Santos" required autocomplete="name">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-client"' +
            ' title="Remove this client" style="min-width:38px;">' +
            '<i class="bi bi-dash-lg"></i></button>';
        namesWrapper.appendChild(row);
        row.querySelector('input').focus();
        refreshRemoveButtons();
    }

    namesWrapper.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-client');
        if (!btn) return;
        btn.closest('.client-name-row').remove();
        refreshRemoveButtons();
    });

    btnAddClient.addEventListener('click', addClientRow);
    refreshRemoveButtons();

    // ── Transaction checkboxes — toggle "Others" textarea ───────────────────
    const otherWrapper  = document.getElementById('other-details-wrapper');
    const otherField    = document.getElementById('transaction_other_details');
    const charCount     = document.getElementById('char-count');
    const txCheckboxes  = document.querySelectorAll('.transaction-checkbox');

    function isOthersChecked() {
        const cb = document.getElementById('tx_others');
        return cb && cb.checked;
    }

    function toggleOtherField() {
        const show = isOthersChecked();
        otherWrapper.style.display = show ? 'block' : 'none';
        otherField.required = show;
        if (!show) {
            otherField.value = '';
            charCount.textContent = '0';
        }
    }

    function updateCharCount() {
        charCount.textContent = otherField.value.length;
    }

    txCheckboxes.forEach(function (cb) {
        cb.addEventListener('change', toggleOtherField);
    });
    otherField.addEventListener('input', updateCharCount);
    toggleOtherField();
    updateCharCount();

    // ── Review modal ─────────────────────────────────────────────────────────
    const form          = document.querySelector('form[action="{{ route('logbook.store') }}"]');
    const btnReview     = document.getElementById('btn-review');
    const btnConfirm    = document.getElementById('btn-confirm-submit');
    const reviewModalEl = document.getElementById('reviewModal');
    const reviewModal   = new bootstrap.Modal(reviewModalEl);

    function val(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function setCell(id, text, isRequired) {
        const cell = document.getElementById(id);
        if (!cell) return;
        if (!text && isRequired) {
            cell.innerHTML = '<span class="text-danger fst-italic">— not provided —</span>';
        } else if (!text) {
            cell.innerHTML = '<span class="text-muted fst-italic">— none —</span>';
        } else {
            cell.textContent = text;
        }
    }

    function setCellHtml(id, html, isRequired) {
        const cell = document.getElementById(id);
        if (!cell) return;
        if (!html && isRequired) {
            cell.innerHTML = '<span class="text-danger fst-italic">— not provided —</span>';
        } else if (!html) {
            cell.innerHTML = '<span class="text-muted fst-italic">— none —</span>';
        } else {
            cell.innerHTML = html;
        }
    }

    function populateReview() {
        setCell('rv-firm_name',      val('firm_name'),      true);
        setCell('rv-gender',         val('gender'),         true);
        setCell('rv-address',        val('address'),        true);
        setCell('rv-contact_number', val('contact_number'), true);
        setCell('rv-email',          val('email'),          false);

        // Collect all client names
        const nameInputs = namesWrapper.querySelectorAll('input[name="client_name[]"]');
        const names = Array.from(nameInputs)
            .map(function (i) { return i.value.trim(); })
            .filter(Boolean);
        if (names.length > 1) {
            const listHtml = '<ol class="mb-0 ps-3">' +
                names.map(function (n) { return '<li>' + escHtml(n) + '</li>'; }).join('') +
                '</ol>';
            setCellHtml('rv-client_name', listHtml, true);
        } else {
            setCell('rv-client_name', names[0] || '', true);
        }

        // Collect checked transaction types
        const checked = Array.from(txCheckboxes)
            .filter(function (cb) { return cb.checked; })
            .map(function (cb) { return cb.value; });
        let txDisplay = '';
        if (checked.length) {
            const withOther = checked.map(function (t) {
                if (t === 'Others') {
                    const detail = otherField.value.trim();
                    return detail ? 'Others: ' + detail : 'Others';
                }
                return t;
            });
            if (withOther.length > 1) {
                txDisplay = '<ul class="mb-0 ps-3">' +
                    withOther.map(function (t) { return '<li>' + escHtml(t) + '</li>'; }).join('') +
                    '</ul>';
                setCellHtml('rv-transaction', txDisplay, true);
            } else {
                setCell('rv-transaction', withOther[0], true);
            }
        } else {
            setCell('rv-transaction', '', true);
        }
    }

    function escHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    btnReview.addEventListener('click', function () {
        populateReview();
        reviewModal.show();
    });

    btnConfirm.addEventListener('click', function () {
        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting…';
        form.submit();
    });

}());
</script>
@endpush
