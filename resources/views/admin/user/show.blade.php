@extends('subsystem::layouts.app')

@section('pageTitle', st('User information'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-4">
            @include('subsystem::admin.user.summary-card')

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><strong>{{st('Manage actions')}}</strong></div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->status->value !== 'banned')
                            <a class="btn btn-primary btn-sm" href="{{route('admin.userManageBalance', ['user' => $user, 'type' => 'increase'])}}">
                                {{st('increase balance')}}
                            </a>
                        @else
                            <a class="btn btn-dark btn-sm">
                                {{st('active user to increase balance')}}
                            </a>
                        @endif

                        <a class="btn btn-warning btn-sm" href="{{route('admin.userManageBalance', ['user' => $user, 'type' => 'decrease'])}}">
                            {{st('decrease balance')}}
                        </a>

                        @if($user->status->value !== 'banned')
                            <a class="btn btn-outline-danger btn-sm" 
                                href="{{route('admin.user.updateStatus', ['user' => $user, 'status' => 'banned'])}}"
                                onclick="return confirm('{{st('Are you sure you want to ban this user?')}}')" >
                            {{st('Ban user')}}</a>
                        @else
                            <a class="btn btn-outline-success btn-sm" 
                                href="{{route('admin.user.updateStatus', ['user' => $user, 'status' => 'active'])}}"
                                onclick="return confirm('{{st('Are you sure you want to active this user?')}}')" >
                                {{st('Activate user')}}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="bi bi-wallet2 me-2"></i>{{st('Financial status')}}</h5>
                    <div class="row">
                        <div class="col-sm-4 text-center border-end">
                            <p class="mb-1 text-muted">{{st('Balance')}} ({{ $user->currency->displayTranslate() }})</p>
                            <h4 class="fw-bold">{{ number_format($user->balance_to_display) }}</h4>
                        </div>
                        <div class="col-sm-4 text-center">
                            <p class="mb-1 text-muted">{{st('Referred users count')}}</p>
                            <h4 class="fw-bold">{{ $user->referred_users_count ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">{{st('User details')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">
                                {{st('Email')}}
                                @if($user->email_verified_at)
                                    <i class="fa fa-check-circle text-success ms-1"></i> 
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-9  left-to-right text-start">
                            <span class="text-muted">
                                {{ $user->email ?? st('Not submited') }}
                            </span>
                            @if($user->email_verified_at)
                                <br/>
                                <span class="text-success ms-1">
                                    {{st('Verified at')}}
                                    {{toJalaliDate($user->email_verified_at)}}
                                </span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">
                                {{st('Mobile')}}
                                @if($user->mobile_verified_at)
                                    <i class="fa fa-check-circle text-success ms-1"></i> 
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-9  left-to-right text-start">
                            <span class="text-muted left-to-right text-start">
                                {{ $user->mobile ?? st('Not submited') }}
                            </span>
                            @if($user->mobile_verified_at) 
                                <br/>
                                <span class="text-success ms-1">
                                    {{st('Verified at')}}
                                    {{toJalaliDate($user->mobile_verified_at)}}
                                </span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0">{{st('Referral code')}}</p></div>
                        <div class="col-sm-9"><span class="badge bg-light text-dark border">{{ $user->referral_code ?? '---' }}</span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0">{{st('Created')}}</p></div>
                        <div class="col-sm-9 text-muted small left-to-right text-start">
                            {{ toJalaliDate($user->created_at, 'Y/m/d H:i:s')}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0">{{st('Last activity')}}</p></div>
                        <div class="col-sm-9 text-muted small left-to-right text-start">
                            {{ $user->last_activity ? toJalaliDate($user->last_activity, 'Y/m/d H:i:s') : st('Without activity')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-form.card>
            <div class="d-flex justify-content-end gap-2">
                {{ html()->a(route('admin.user.list'), st('menu.User list'))->class('btn btn-outline-secondary') }}
            </div>
        </x-form.card>
    </div>
</div>
@endsection