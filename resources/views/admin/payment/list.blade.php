@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Payment list'))

@section('formFields')
    <x-form.row>
        <x-form.input name="nickname" :value="app('request')->input('nickname')" :placeholder="st('Nickname')" col="col-auto" />
        <x-form.input name="gateway_reference" :value="app('request')->input('gateway_reference')" :placeholder="st('Gateway reference')" col="col-auto" />
        <x-form.input name="payment_gateway_id" type="select" :value="app('request')->input('payment_gateway_id')" :placeholder="st('All gateways')" :options="$paymentGateways" col="col-auto" />
        <x-form.input name="status" type="select" :value="app('request')->input('status', 'verified')" :placeholder="st('All statuses')" :options="$statuses" col="col-auto" />
    </x-form.row>
@endsection
