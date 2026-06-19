@extends('subsystem::layouts.dataTable')
@section('pageTitle',st('menu.Faq category list'))
@section('formFields')
    <div class="col-lg-12 mb-3">
        <div class="form-group row">
            <div class="col-auto mb-2">
                <div class="input-group">
                    {{ html()->select('status',['unarchived'=>st('Unarchived'),'archived' => st('Archived'),'all' => st('All')] ,app('request')->input('status'))->class('form-control') }}
                </div>
            </div>
            <div class="col-auto mb-2">
                <div class="input-group">
                    {{ html()->select('language', $language,app('request')->input('language'))->class('form-control')->placeholder(st('language')) }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('btn')
    <a href="{{route('admin.faqCategory.create')}}" class="btn btn-secondary ml-2">{{st('Add faq category')}}</a>
@endsection

