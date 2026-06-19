@extends('subsystem::layouts.app')

@section('pageTitle', $article->exists ? st('Edit article') : st('Add article'))

@section('content')

    {!! html()->form('POST', $article->exists ? route('admin.article.update', $article) : route('admin.article.store'))->acceptsFiles()->open() !!}

    <x-form.card :title="st('General information')">
        <x-form.row>
            <x-form.input name="title" :label="st('Title')" :value="old('title', $article->title)" required />
            <x-form.input name="reading_time" :label="st('Reading time')" :value="old('reading_time', $article->reading_time)" />
        </x-form.row>

        <x-form.row>
            <x-form.input name="slug" :label="st('Slug')" :value="old('slug', $article->slug)" required />
            <div class="col-md-6">
                <x-subsystem::file-preview name="poster" :filePath="$article->posterSID
                    ? route('storage.download', ['type' => 'thumbnail', 'SID' => $article->posterSID])
                    : ''" :label="st('Poster')" />
            </div>
        </x-form.row>

        <x-form.row>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <x-subsystem::multi-select name="articleCategories" :options="$articleCategories" :selected="$currentCategories ?? []"
                        label="{{ st('Add Article Category') }} *" />
                    @error('articleCategories')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @if (count($languages) > 1)
                <x-form.input name="language" :label="st('Language')" :options="$languages" :value="old('language', $article->language)" required />
            @else
                {{ html()->hidden('language', array_keys($languages)[0]) }}
            @endif
        </x-form.row>
    </x-form.card>

    <x-form.card :title="st('Article Content')">
        <x-form.row>
            <x-form.input name="introduction" :label="st('Introduction')" type="textarea" :value="old('introduction', $article->introduction)" :textarea-attrs="['rows' => 10]"
                col="col-md-12" required />
        </x-form.row>

        <x-form.row>
            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <x-subsystem::tiny-mce name="content" modelName="article" :value="$article->content" isImageAllowed="true"
                        label="{{ st('Article Content') }} *" />
                        @error('content')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                </div>
            </div>
        </x-form.row>
    </x-form.card>

    <x-form.card :title="st('SEO settings')">
        <x-form.row>
            <x-form.input name="meta_title" :label="st('Meta title')" :value="old('meta_title', $article->meta_title)" col="col-md-12" />
        </x-form.row>

        <x-form.row>
            <x-form.input name="meta_description" :label="st('Meta description')" type="textarea" :value="old('meta_description', $article->meta_description)" col="col-md-12"
                :textarea-attrs="['rows' => 3]" />
        </x-form.row>

        <x-form.row>
            <x-form.input name="meta_keyword" :label="st('Meta keyword')" :value="old('meta_keyword', $article->meta_keyword)" col="col-md-12" />
        </x-form.row>
    </x-form.card>

    <x-form.card>
        <div class="d-flex justify-content-end gap-2">
            {{ html()->submit($article->exists ? st('Update') : st('Submit'))->class('btn btn-primary shadow-sm') }}
            {{ html()->a(route('admin.article.list'), $article->exists ? st('Return') : st('Article list'))->class('btn btn-outline-secondary') }}
        </div>
    </x-form.card>

    {!! html()->form()->close() !!}
@endsection
