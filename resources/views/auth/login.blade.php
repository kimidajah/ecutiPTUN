@extends('layouts.app')

@section('content')
<div class="container-fluid vh-100 p-0" 
     style="min-height:100vh;
            background: linear-gradient(135deg, #b5f4b0 0%, #fdfcfb 70%, #fff7e6 100%);">
    <div class="row h-100">
        {{-- Bagian kiri: background image --}}
        <div class="col-md-6 d-none d-md-block p-0">
            <div class="h-100 w-100" 
                style="background-image: url('{{ asset('images/homepage.svg') }}');
                       background-size: cover;
                       background-position: center;">
            </div>
        </div>

        {{-- Bagian kanan: form login --}}
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="p-5 rounded-4 shadow"
                 style="width: 75%;
                        background: rgba(255, 255, 255, 0.4);
                        backdrop-filter: blur(15px);
                        -webkit-backdrop-filter: blur(15px);
                        border: 1px solid rgba(255, 255, 255, 0.3);">
                
                <div x-data="{ showPassword: false }">
                    <h2 class="mb-4 text-center fw-bold">{{ __('Login') }}</h2>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autofocus
                                style="background: rgba(255,255,255,0.7); border: none;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <input :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password"
                                    style="background: rgba(255,255,255,0.7); border: none;">
                                <button type="button" class="btn btn-outline-secondary"
                                    @click="showPassword = !showPassword">
                                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link text-center" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
