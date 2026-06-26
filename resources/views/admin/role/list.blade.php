@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Role list'))

@section('btn')
    <a href="{{ route('admin.role.create') }}" class="btn btn-secondary ml-2">{{ st('Add role') }}</a>
@endsection
 