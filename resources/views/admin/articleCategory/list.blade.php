@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('menu.Article categories'))

@section('formFields')
    <x-form.row>
        <x-form.input name="title" :value="app('request')->input('title')" :placeholder="st('Title')" col="col-auto" />
        <x-form.input name="slug" :value="app('request')->input('slug')" :placeholder="st('Slug')" col="col-auto" />
        <x-form.input name="status" type="select" :value="app('request')->input('status')" :placeholder="st('All statuses')" :options="$statuses" col="col-auto" />
    </x-form.row>
@endsection

@section('btn')
    <a href="{{ route('admin.articleCategory.create') }}" class="btn btn-secondary ml-2">{{ st('Add Article Category') }}</a>
@endsection
