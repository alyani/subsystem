@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Transaction list'))
@if (isset($user))
    @section('pageSubTitle', empty($user->name) ? $user->nickname : $user->name . ' ' . $user->family)
@endif

@section('formFields')
    <x-form.row>
        <x-form.input name="nickname" :value="$user->nickname ?? app('request')->input('nickname')" :placeholder="st('Nickname')" col="col-auto" />
        <x-form.input name="user_id" :value="app('request')->input('user_id')" type="hidden"  col="col-auto" />
    </x-form.row>
@endsection
