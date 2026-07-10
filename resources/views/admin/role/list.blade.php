@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Role list'))

@can('admin.role.create')
    @section('btn')
        <a href="{{ route('admin.role.create') }}" class="btn btn-secondary ml-2">{{ st('Add role') }}</a>
    @endsection
@endcan
 