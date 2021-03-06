@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('2FA - OTP') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>2FA is <strong>{{ $user->two_factor_enabled ? 'enabled' : 'disabled' }}</strong>.</p>

                    <form method="POST" action="{{ route('2fa.otp.verify') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col">
                                <input type="number" class="form-control @error('otp') is-invalid @enderror" 
                                    id="otp" name="otp" placeholder="Enter OTP" pattern="[0-9]{6}" required autofocus />

                                @error('otp')
                                    <span class="invalid-feedback" role="alert">
                                        <small>{{ $message }}</small>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            @if(! $user->two_factor_enabled)
                                <div class="col-md-4">
                                    <a class="btn btn-secondary" href="{{ route('2fa.qrcode') }}">
                                        {{ __('Setup 2FA') }}
                                    </a>
                                </div>
                            @endif
                            
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm OTP') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Session') }}</div>
                <div class="card-body">
                    <pre>{!! json_encode(request()->session()->all(), JSON_PRETTY_PRINT) !!}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
