@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('2FA - QR Code') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>OTP is <strong>{{ Auth::user()->google2fa_enabled ? 'enabled' : 'disabled' }}</strong>.</p>

                    <p>Set up your two factor authentication by scanning the barcode below with you Google Authenticator app.</p>

                    <small>Alternatively, you can use the code <strong>{{ $google2fa_secret }}</strong> </small>

                    <div>
                        {!! $qrcode_svg !!}
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
    </div>
</div>
@endsection
