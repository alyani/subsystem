@extends('subsystem::layouts.app')

@section('pageTitle', st('Add Faq Category'))

@section('content')
    <div class="card">
        <div class="card-body">
            {!! html()->form('POST', route('admin.faqCategory.update',$faqCategory->ID))->open() !!}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Title') . ' *', 'name')->class('control-label') }}
                        {{ html()->text('title', old('title',$faqCategory->title))->class('form-control')->placeholder(st('Title')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('slug') . ' *', 'slug')->class('control-label') }}
                        {{ html()->text('slug', old('slug',$faqCategory->slug))->class('form-control')->placeholder(st('courseSlug')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Sort Order'))->class('control-label') }}
                        {{ html()->number('sort_order', old('sort_order', $faqCategory->sort_order))->class('form-control')->placeholder(st('Sort Order')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('language') . ' *', 'slug')->class('control-label') }}
                        {{ html()->select('language', $language,old('language',$faqCategory->language))->class('form-control')->placeholder(st('language')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-right mt-2">
                <div class="btn-group gap-2" role="group">
                    {{ html()->submit(st('submit'))->class('btn btn-primary') }}
                    {{ html()->a(route('admin.faqCategory.list'), st('Return'))->class('btn btn-secondary') }}
                </div>
            </div>
            {!! html()->form()->close() !!}
        </div>
    </div>
@endsection
