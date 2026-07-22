@extends('subsystem::layouts.ipg')

@section('content')
    <div class="callback-card p-4 p-md-5 text-center">
        @if($status === 'success')
            <div class="callback-icon text-success">
                ✓
            </div>

            <h4 class="mb-3">
                {{ st('Payment successful') }}
            </h4>
        @else
            <div class="callback-icon text-danger">
                ×
            </div>

            <h4 class="mb-3">
                {{ st('Payment failed') }}
            </h4>
        @endif

        @if(!empty($message))
            <p class="callback-message mb-4">
                {{ $message }}
            </p>
        @endif

        @if(!empty($returnUrl))
            <a
                href="{{ $returnUrl }}"
                class="btn btn-primary px-4"
            >
                {{ st('Return to website') }}
            </a>
        @endif

    </div>
@endsection
