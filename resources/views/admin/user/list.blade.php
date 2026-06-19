@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Users'))

@section('formFields')
    <x-form.row>
        <x-form.input name="mobile" :value="app('request')->input('mobile')" :placeholder="st('Mobile')" col="col-auto" />
        <x-form.input name="status" type="select" :value="app('request')->input('status')" :placeholder="st('All statuses')" :options="$statuses" col="col-auto" />
    </x-form.row>
@endsection
