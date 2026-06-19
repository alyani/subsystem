@extends('subsystem::layouts.app')

@section('pageTitle', $type == 'decrease' ? st('decrease user balance') : st('increase user balance'))

@section('content')
<div class="row">
    <div class="col-lg-4">
        @include('subsystem::admin.user.summary-card')
    </div>
    <div class="col-lg-8">
        {!! html()->form('POST', route('admin.userManageBalance.' . $type, $user))->open() !!}
        <x-form.card>
            <x-form.row>
                <x-form.input 
                    name="current_balance" 
                    :label="st('User balance') . ' ' . $user->currency->displayTranslate()" 
                    :value="number_format($user->balance_to_display)"
                    :static=1 {{-- show text --}}
                />
            </x-form.row>

            <x-form.row>
                <x-form.input name="amount" :label="st('amount') . ' ' . $user->currency->displayTranslate()" type="number" required />
                <x-form.input name="currency" type="hidden" :value="$user->currency->display()" />
            </x-form.row>

            <x-form.row>
                <x-form.input name="description" :label="st('Description')" type="textarea" :textarea-attrs="['rows' => 4]"/>
            </x-form.row>
        </x-form.card>

        <x-form.card>
            <div class="d-flex justify-content-end gap-2">
                @if ($type == 'decrease')
                    {{ html()->submit(st('decrease balance'))->class('btn btn-warning shadow-sm') }}
                @else
                    {{ html()->submit(st('increase balance'))->class('btn btn-success shadow-sm') }}
                @endif
                {{ html()->a(route('admin.user.show', $user), st('Return') )->class('btn btn-outline-secondary') }}
            </div>
        </x-form.card>
        {!! html()->form()->close() !!}
    </div>
@endsection
