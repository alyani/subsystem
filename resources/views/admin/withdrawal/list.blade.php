@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Withdrawal list'))

@section('formFields')
    <x-form.row>
        <x-form.input name="nickname" :value="app('request')->input('nickname')" :placeholder="st('Nickname')" col="col-auto" />
    </x-form.row>
@endsection
