@extends('subsystem::layouts.app')

@section('pageTitle', st('Add Faq'))

@section('content')
    <div class="card">
        <div class="card-body">
            {!! html()->form('POST', route('admin.faq.update',$faq->ID))->open() !!}
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Question') . ' *', 'question')->class('control-label') }}
                        {{ html()->textarea('question', old('question',$faq->question))->class('form-control')->rows(2)->placeholder(st('Enter the question')) }}
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <x-subsystem::tiny-mce name="answer" label="{{ st('Answer') }} *" value="{{ old('answer', $faq->answer) }}"/>
                    </div>
                </div>
                @if(!request('categoryID'))
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            {{ html()->label(st('Category'), 'categoryID')->class('control-label') }}
                            {{ html()->select('categoryID', $faqCategories ?? [],old('categoryID',$faq->categoryID))->class('form-control')->placeholder(st('Select category')) }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            {{ html()->label(st('language') . ' *', 'slug')->class('control-label') }}
                            {{ html()->select('language', $language,old('language', $faq->language))->class('form-control')->placeholder(st('language')) }}
                        </div>
                    </div>
                @else
                    {{ html()->hidden('faqCategoryID', request('categoryID')) }}
                @endif

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Sort Order'), 'sort_order')->class('control-label') }}
                        {{ html()->number('sort_order', old('sort_order', $faq->sort_order))->class('form-control')->placeholder(st('Sort Order')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Meta Title'), 'meta_title')->class('control-label') }}
                        {{ html()->text('meta_title', old('meta_title',$faq->meta_title))->class('form-control')->placeholder(st('Meta Title')) }}
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Meta Description'), 'meta_description')->class('control-label') }}
                        {{ html()->textarea('meta_description', old('meta_description',$faq->meta_description))->class('form-control')->rows(3)->placeholder(st('Meta Description')) }}
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Meta Keyword'), 'meta_keyword')->class('control-label') }}
                        {{ html()->text('meta_keyword', old('meta_keyword',$faq->meta_keyword))->class('form-control')->placeholder(st('Meta Keyword')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-right mt-2">
                <div class="btn-group gap-2" role="group">
                    {{ html()->submit(st('Submit'))->class('btn btn-primary') }}
                    {{ html()->a(route('admin.faq.list', ['categoryID' => request('categoryID') ?? null]), st('Return'))
                    ->class('btn btn-secondary') }}
                </div>
            </div>
            {!! html()->form()->close() !!}
        </div>
    </div>
@endsection
