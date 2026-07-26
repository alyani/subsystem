@extends('subsystem::layouts.app')

@section('pageTitle', 'ویرایش پروفایل')
@section('content')
    {!! html()->form('POST', route('admin.profile.update'))->acceptsFiles()->open() !!}
 
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
        </x-form.row>
    </x-form.card>


    <x-form.card title="ویرایش رمز عبور">
        <x-form.row>
            <x-form.input name="currenct_password" label="رمز عبور فعلی" type="password" class=password-input />
            <x-form.input name="password" label="رمز عبور جدید" type="password" class=password-input />
        </x-form.row>
    </x-form.card>

    <x-form.card>
        <div class="d-flex justify-content-end gap-2">
            {{ html()->submit($manager->exists ? st('Update') : st('Submit'))->class('btn btn-primary shadow-sm') }}
        </div>
    </x-form.card>

    {!! html()->form()->close() !!}
@endsection
