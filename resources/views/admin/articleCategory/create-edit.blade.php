@extends('subsystem::layouts.app')

@section('pageTitle', $articleCategory->exists ? st('Edit article category') : st('Add Article Category'))

@section('content')
    {!! html()->form('POST', $articleCategory->exists ? route('admin.articleCategory.update', $articleCategory) : route('admin.articleCategory.store'))->acceptsFiles()->open() !!}
 
    <x-form.card :title="st('General information')">
        <x-form.row>
            <x-form.input name="title" :label="st('Title')" :value="old('title', $articleCategory->title)" required />
            <x-form.input name="status" :label="st('Status')" type="select" :options="$statuses" :value="old('status', $articleCategory->status)" required/>
        </x-form.row>

        <x-form.row>
            <x-form.input name="slug" :label="st('Slug')" :value="old('slug', $articleCategory->slug)" required />
            <x-form.input name="sort_order" :label="st('Sort Order')" type="number" :value="old('sort_order', $articleCategory->sort_order)" />
        </x-form.row>

        <x-form.row>
            <div class="col-md-6">
                <x-subsystem::file-preview name="photo" :filePath="$articleCategory->photoSID
                    ? route('storage.download', ['type' => 'thumbnail', 'SID' => $articleCategory->photoSID])
                    : ''" :label="st('Icon')" />
            </div>
            @if (count($languages) > 1)
                <x-form.input name="language" :label="st('Language')" :options="$languages" :value="old('language', $articleCategory->language)" required />
            @else
                {{ html()->hidden('language', array_keys($languages)[0]) }}
            @endif
        </x-form.row>

        <x-form.row>
            <x-form.input name="description" :label="st('Description')" type="textarea" :value="old('description', $articleCategory->description)"
                :textarea-attrs="['rows' => 7]" />
        </x-form.row>
    </x-form.card>

    <x-form.card :title="st('SEO settings')">
        <x-form.row>
            <x-form.input name="meta_title" :label="st('Meta title')" :value="old('meta_title', $articleCategory->meta_title)" col="col-md-12" />
        </x-form.row>

        <x-form.row>
            <x-form.input name="meta_description" :label="st('Meta description')" type="textarea" :value="old('meta_description', $articleCategory->meta_description)" col="col-md-12"
                :textarea-attrs="['rows' => 3]" />
        </x-form.row>

        <x-form.row>
            <x-form.input name="meta_keyword" :label="st('Meta keyword')" :value="old('meta_keyword', $articleCategory->meta_keyword)" col="col-md-12" />
        </x-form.row>
    </x-form.card>

    <x-form.card>
        <div class="d-flex justify-content-end gap-2">
            {{ html()->submit($articleCategory->exists ? st('Update') : st('Submit'))->class('btn btn-primary shadow-sm') }}
            {{ html()->a(route('admin.articleCategory.list'), $articleCategory->exists ? st('Return') : st('Article category list'))->class('btn btn-outline-secondary') }}
        </div>
    </x-form.card>

    {!! html()->form()->close() !!}
@endsection
