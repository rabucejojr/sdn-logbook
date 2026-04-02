@extends('layouts.public')

@section('title', 'Admin Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-4 col-md-6">

        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:64px; height:64px; background: var(--dost-blue);">
                <i class="bi bi-shield-lock-fill" style="font-size:1.75rem; color:#fff;"></i>
            </div>
            <h4 class="fw-bold mb-0" style="color: var(--dost-blue);">Admin Login</h4>
            <p class="text-muted" style="font-size:0.875rem;">DOST SDN Client Visit Logbook</p>
        </div>

        <div class="content-card">

            {{-- Auth error --}}
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        autofocus
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:0.875rem;">Remember me</label>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('logbook.index') }}" class="text-muted" style="font-size:0.82rem; text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>Back to Client Form
            </a>
        </div>

    </div>
</div>
@endsection
