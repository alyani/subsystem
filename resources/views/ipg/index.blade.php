@extends('subsystem::layouts.ipg')
@section('content')
    <div class="container payment-container">
        <div class="error-page">
            <div class="loading-box">
                <div class="sk-chase">
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                </div>
            </div>
            <div class="desc-text">
                <div class="title">
                    {{ __('finance::messages.Redirecting to gateway') }}
                </div>
            </div>
            <form id="chargeGateway" method="{{ $gateway['method']  }}" action="{{ $gateway['url'] }}">
                @foreach ($gateway['params'] as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}"/>
                @endforeach
            </form>
            <script>
                document.getElementById('chargeGateway').submit();
            </script>
        </div>
    </div>
@endsection