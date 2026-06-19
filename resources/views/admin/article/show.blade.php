@extends('subsystem::layouts.app')

@section('pageTitle', st('Show article', ['title' => $article->title]))

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Title') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->title)->class('form-control-plaintext fs-6') }}
                </div>
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Manager') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->manager->name . ' ' . $article->manager->family)->class('form-control-plaintext fs-6') }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Slug') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->slug)->class('form-control-plaintext fs-6') }}
                </div>
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Reading time') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->reading_time)->class('form-control-plaintext fs-6') }}
                </div>
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('language') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span(st($article->language))->class('form-control-plaintext fs-6') }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Meta title') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->meta_title)->class('form-control-plaintext fs-6') }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Meta keyword') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->meta_keyword)->class('form-control-plaintext fs-6') }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Meta description') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->meta_description)->class('form-control-plaintext fs-6') }}
                </div>
            </div>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($article->posterSID)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        {{ html()->label(st('Poster') . ':')->class('control-label fw-bold fs-7') }}
                        {{ html()->img(route('storage.download', ['type' => 'original', 'SID' => $article->posterSID]))->class('form-control-plaintext')->style('height: 300px; width: auto;') }}
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Introduction') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->span($article->introduction)->class('form-control-plaintext fs-6') }}
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    {{ html()->label(st('Article Content') . ':')->class('control-label fw-bold fs-7') }}
                    {{ html()->div($article->content)->class('form-control-plaintext fs-6') }}
                </div>
            </div>
        </div>
    </div>
@endsection
