@extends('subsystem::layouts.app')

@section('pageTitle', $role->exists ? st('Edit role') : st('Add role'))
@section('fullErrorMessage', 'yes')
@php
    $selectedPermissions = old(
        'permissions',
        $role->permissions?->pluck('name')->toArray() ?? []
    );
@endphp

@section('content')

    {!! html()->form('POST', $role->exists ? route('admin.role.update', $role) : route('admin.role.store'))->acceptsFiles()->open() !!}

    <x-form.card>
        <x-form.row>
            <x-form.input name="name" :label="st('Name')" :value="old('name', $role->name)" required />
        </x-form.row>
    </x-form.card>


    <div class="row g-4">
        @foreach($permissions as $group => $groupPermissions)
            <div class="col-md-6">
                <x-form.card :title="st($group, [], 'permissions')">

                    <div class="mb-3">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary select-all"
                                data-group="{{ $group }}">
                            انتخاب همه
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-secondary unselect-all"
                                data-group="{{ $group }}">
                            حذف انتخاب
                        </button>
                    </div>

                    <div class="row g-2 permission-group" data-group="{{ $group }}">
                        @foreach($groupPermissions as $permission => $title)
                            <div class="col-12">
                                <div class="form-check">
                                    {{ html()
                                        ->checkbox(
                                            'permissions[]',
                                            in_array($permission, $selectedPermissions),
                                            $permission
                                        )
                                        ->class('form-check-input permission-checkbox')
                                        ->id('permission_'.$permission)
                                    }}

                                    <label class="form-check-label"
                                        for="permission_{{ $permission }}">
                                        {{ st($title, [], 'permissions') }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </x-form.card>
            </div>
        @endforeach
    </div>

    <x-form.card>
        <div class="d-flex justify-content-end gap-2">
            {{ html()->submit($role->exists ? st('Update') : st('Submit'))->class('btn btn-primary shadow-sm') }}
            {{ html()->a(route('admin.role.list'), $role->exists ? st('Return') : st('menu.Role list'))->class('btn btn-outline-secondary') }}
        </div>
    </x-form.card>

    {!! html()->form()->close() !!}
@endsection

@push('js')
<script>
$(function () {

    $('.select-all').on('click', function () {
        $('.permission-group[data-group="' + $(this).data('group') + '"]')
            .find('.permission-checkbox')
            .prop('checked', true);
    });

    $('.unselect-all').on('click', function () {
        $('.permission-group[data-group="' + $(this).data('group') + '"]')
            .find('.permission-checkbox')
            .prop('checked', false);
    });

    $('#selectAllPermissions').on('click', function () {
        $('.permission-checkbox').prop('checked', true);
    });

    $('#unselectAllPermissions').on('click', function () {
        $('.permission-checkbox').prop('checked', false);
    });

});
</script>
@endpush
