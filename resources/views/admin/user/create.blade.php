@extends('subsystem::layouts.app')

@section('pageTitle', st('Add user'))
@section('fullErrorMessage', 'yes')

@section('content')
    <div class="card">
        <div class="card-body">
            {{ html()->form('POST', route('admin.user.store'))->class('form')->acceptsFiles()->open() }}

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Name') . ' *', 'name')->class('control-label') }}
                        {{ html()->text('name', old('name'))->class('form-control')->placeholder(st('Name')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Family') . ' *', 'family')->class('control-label') }}
                        {{ html()->text('family', old('family'))->class('form-control')->placeholder(st('Family')) }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('country code') . ' *', 'country_code')->class('control-label') }}
                        {{ html()->text('country_code', old('country_code'))->class('form-control country-code-input')->placeholder(st('country code')) }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Mobile') . ' *', 'mobile')->class('control-label') }}
                        {{ html()->text('mobile', old('mobile'))->class('form-control mobile-input')->placeholder(st('Mobile')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Email'), 'email')->class('control-label') }}
                        {{ html()->text('email', old('email'))->class('form-control')->placeholder(st('Email')) }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Phone'), 'phone')->class('control-label') }}
                        {{ html()->text('phone', old('phone'))->class('form-control phone-input')->placeholder(st('Phone')) }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        {{ html()->label(st('Password') . ' *', 'password')->class('control-label') }}
                        {{ html()->password('password')->class('form-control password-input')->placeholder(st('Password')) }}
                    </div>
                </div>
            </div>

            <div class="text-right">
                <div class="btn-group" role="group">
                    {{ html()->submit(st('Add user'))->class('btn btn-primary mt-2') }}

                </div>
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
@endsection
