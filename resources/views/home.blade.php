@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>OTP is <strong>{{ Auth::user()->google2fa_enabled ? 'enabled' : 'disabled' }}</strong>.</p>

                    <form id="toogle-2fa-form" action="{{ route('2fa.toogle') }}" method="POST">
                        @csrf
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" 
                                id="google2fa-enabled" name="google2fa_enabled" value="1" 
                                @if(Auth::user()->google2fa_enabled) checked @endif>
                            <label class="form-check-label" for="google2fa-enabled"> 2-Step Authentication</label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra-js')
    <script>
        (function() {
            document.getElementById('google2fa-enabled')
                .addEventListener('change', function() {
                    document.getElementById('toogle-2fa-form').submit();
                });
        })();
    </script>
@endpush