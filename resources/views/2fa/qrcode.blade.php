@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('2FA - QR Code') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col">
                            <p>2FA is <strong>{{ $user->two_factor_enabled ? 'enabled' : 'disabled' }}</strong>.</p>

                            <p>Set up your two factor authentication by scanning the barcode below with your Authenticator app.</p>

                            <small>Alternatively, you can use the code <code>{{ $two_factor_secret }}</code> </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            {!! $qrcode_svg !!}
                        </div>

                        <div class="col">
                            <h4>Recovery Codes</h4>
                            @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes, true)) as $code)
                                <code>{{ trim($code) }}</code><br/>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-4">
                            <a class="btn btn-default" href="{{ route('home') }}">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-primary" href="{{ route('2fa.otp') }}">
                                {{ __('Next') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- $user->recoveryCodes() --}}

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
