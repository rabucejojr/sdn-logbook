@extends('layouts.public')

@section('title', 'Visit Logged Successfully')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="content-card text-center py-5">

            {{-- Success icon --}}
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                     style="width:80px; height:80px; background-color:#d1fae5;">
                    <i class="bi bi-check-circle-fill" style="font-size:2.5rem; color:#10b981;"></i>
                </div>
            </div>

            <h3 class="fw-bold mb-2" style="color: var(--dost-blue);">Visit Logged Successfully!</h3>

            <p class="text-muted mb-1">Thank you for visiting DOST Surigao del Norte.</p>
            <p class="text-muted mb-4">Your visit has been recorded on
                <strong>{{ now()->format('F d, Y \a\t h:i A') }}</strong>.
            </p>

            <a href="{{ route('logbook.index') }}" class="btn btn-primary px-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Logbook
            </a>

        </div>
    </div>
</div>
@endsection
