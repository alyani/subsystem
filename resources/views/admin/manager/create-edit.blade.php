@extends('subsystem::layouts.app')

@section('pageTitle', $manager->exists ? st('Edit manager') : st('Add manager'))
@section('content')
    {!! html()->form('POST', $manager->exists ? route('admin.manager.update', $manager) : route('admin.manager.store'))->acceptsFiles()->open() !!}
 
    <x-form.card>
        <x-form.row>
            <x-form.input name="name" :label="st('Name')" :value="old('name', $manager->name)" required />
            <x-form.input name="mobile" :label="st('Mobile')" :value="old('mobile', $manager->mobile)" class="mobile-input" required/>
        </x-form.row>

        <x-form.row>
            <x-form.input name="family" :label="st('Family')" :value="old('family', $manager->family)" required />
            <x-form.input name="email" :label="st('Email')" type="email" :value="old('email', $manager->email)" />
        </x-form.row>

        <x-form.row>
            <div class="col-md-6">
                <x-subsystem::file-preview name="avatar" :filePath="$manager->avatarSID
                    ? route('storage.download', ['type' => 'thumbnail', 'SID' => $manager->avatarSID])
                    : ''" :label="st('Avatar')" />
            </div>
            <x-form.input name="password" :label="st('Password')" type="password" class=password-input />
        </x-form.row>

        @if ($manager->exists && auth()->id() != $manager->id) 
        <x-form.row>
            <x-form.input name="status" :label="st('Status')" type="select" :options="$statuses" :value="old('status', $manager->status)" required/>
        </x-form.row>
        @endif
    </x-form.card>

    <x-form.card>
        <div class="d-flex justify-content-end gap-2">
            {{ html()->submit($manager->exists ? st('Update') : st('Submit'))->class('btn btn-primary shadow-sm') }}
            {{ html()->a(route('admin.manager.list'), $manager->exists ? st('Return') : st('Managers list'))->class('btn btn-outline-secondary') }}
        </div>
    </x-form.card>

    {!! html()->form()->close() !!}
@endsection
