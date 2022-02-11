@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('2FA') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ Auth::user()->google2fa_enabled ? 'enabled' : 'disabled' }}</p>

                    <h3>{{ Auth::user()->google2fa_secret }}</h3>

                    <p>show next button to "show enter otp form"</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
