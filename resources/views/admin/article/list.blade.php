@extends('subsystem::layouts.dataTable')

@section('pageTitle', st('Articles list'))

@section('formFields')
    <x-form.row>
        <x-form.input name="title" :value="app('request')->input('title')" :placeholder="st('Title')" col="col-auto" />
        <x-form.input name="slug" :value="app('request')->input('slug')" :placeholder="st('Slug')" col="col-auto" />
        <x-form.input name="article_category_id" type="select" :value="app('request')->input('article_category_id')" :placeholder="st('All categories')" :options="$articleCategories" col="col-auto" />
    </x-form.row>
@endsection

@section('btn')
    <a href="{{ route('admin.article.create') }}" class="btn btn-secondary ml-2">{{ st('Add article') }}</a>
@endsection
