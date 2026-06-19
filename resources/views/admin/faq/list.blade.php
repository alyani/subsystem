@extends('subsystem::layouts.dataTable')
@section('pageTitle',st('menu.Faq list'))
@section('btn')
    <a href="{{route('admin.faq.create',['categoryID' => request('categoryID') ?? null])}}" class="btn btn-secondary ml-2">{{st('Add faq')}}</a>
@endsection

@section('formFields')
    <div class="col-lg-12 mb-3">
        <div class="form-group row">
            <div class="col-auto mb-2">
                <div class="input-group">
                    {{ html()->select('language', $language,app('request')->input('language'))->class('form-control')->placeholder(st('language')) }}
                </div>
            </div>
        </div>
    </div>
@endsection
