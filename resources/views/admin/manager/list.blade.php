@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Manager list'))

@section('formFields')
    <x-form.row>
        <x-form.input name="mobile" :value="app('request')->input('mobile')" :placeholder="st('Mobile')" col="col-auto" />
        <x-form.input name="status" type="select" :value="app('request')->input('status')" :placeholder="st('All statuses')" :options="$statuses" col="col-auto" />
        <x-form.input name="role_id" type="select" :value="app('request')->input('role_id')" :placeholder="st('All roles')" :options="$roles" col="col-auto" />
    </x-form.row>
@endsection

@section('btn')
    <a href="{{ route('admin.manager.create') }}" class="btn btn-secondary ml-2">{{ st('Add manager') }}</a>
@endsection
 