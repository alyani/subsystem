<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/subsystem/images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/subsystem/images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('vendor/subsystem/images/logo.png') }}">

    <title>{{config('subsystem.appName')}}</title>

    <link href="{{url('vendor/subsystem/fonts/vazir/font-face.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('vendor/subsystem/icons/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('vendor/subsystem/icons/phosphor/styles.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('vendor/subsystem/css/rtl/all.min.css')}}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{url('vendor/subsystem/css/rtl/style.css')}}" id="stylesheet" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{url('vendor/subsystem/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet"
          href="{{url('vendor/subsystem/plugins/datatables-fixedheader/css/fixedHeader.bootstrap4.css')}}">
    <link rel="stylesheet"
          href="{{url('vendor/subsystem/plugins/datatables-responsive/css/responsive.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{ url('vendor/subsystem/plugins/datepicker/css/lib/persian-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendor/subsystem/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendor/subsystem/plugins/select2/css/select2-bootstrap4.css') }}">
    <link rel="stylesheet" href="{{url('vendor/subsystem/css/custom.css')}}" id="stylesheet" type="text/css">

    <!-- Theme JS files -->
    <script src="{{url('vendor/subsystem/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{url('vendor/subsystem/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/datatables-fixedheader/js/dataTables.fixedHeader.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/datatables-responsive/js/dataTables.responsive.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/datatables-responsive/js/responsive.bootstrap4.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ url('vendor/subsystem/plugins/datepicker/js/lib/jalalidatepicker.min.js') }}"></script>
    <script src="{{url('vendor/subsystem/js/custom.js')}}"></script>

    <script src="{{url('vendor/subsystem/js/app.js')}}"></script>
    <!-- /theme JS files -->
</head>

<body class="">

<!-- Page content -->
<div class="page-content">

    <!-- Main sidebar -->
    <div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

        <!-- Sidebar header -->
        <div class="sidebar-section bg-black bg-opacity-10 border-bottom-white border-opacity-10">
            <div class="sidebar-logo d-flex justify-content-center align-items-center">
                <a href="#" class="d-inline-flex align-items-center py-2 site-title">
                    {{config('subsystem.appTitle')}}
                </a>

                {{-- <div class="sidebar-resize-hide ms-auto">
                    <button type="button"
                            class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button type="button"
                            class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
                        <i class="ph-x"></i>
                    </button>
                </div> --}}
            </div>
        </div>
        <!-- /sidebar header -->


        <!-- Sidebar content -->
        <div class="sidebar-content">
            <div class="sidebar-user">
                <div class="category-content">
                    <div class="profile-user-info">
                        <a href="#"><img src="{{url('vendor/subsystem/images/user.png')}}"
                                         class="rounded-circle profile-avatar" alt=""></a>
                        <div class="media-body">
                                <span class="media-heading text-semibold">
                                {{auth()->user()?->getFullName()}}
                                </span>
                            <div class="text-size-mini text-muted">{{st('site administrator')}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main navigation -->
            <div class="sidebar-section">
                @include('subsystem::layouts.menu')
            </div>
            <!-- /main navigation -->

        </div>
        <!-- /sidebar content -->

    </div>
    <!-- /main sidebar -->


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Main navbar -->
        <div class="navbar navbar-expand-lg navbar-static shadow">
            <div class="container-fluid">
                <div class="d-flex d-lg-none me-2">
                    <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                        <i class="ph-list"></i>
                    </button>
                </div>

                <div class="navbar-collapse flex-lg-1 order-2 order-lg-1 collapse" id="navbar_search">
                    <div class="navbar-search flex-fill dropdown mt-2 mt-lg-0">
                        <i class="ph-calendar-blank"></i>
                        {{st('today') .' '. verta()->format('l d F %Y')}}
                    </div>
                </div>

                <ul class="nav hstack gap-sm-1 flex-row justify-content-end order-1 order-lg-2">
                    <li class="nav-item d-lg-none">
                        <a href="#navbar_search" class="navbar-nav-link navbar-nav-link-icon rounded-pill"
                           data-bs-toggle="collapse">
                            <i class="ph-magnifying-glass"></i>
                        </a>
                    </li>

                    <li class="nav-item nav-item-dropdown-lg dropdown">
                        <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="status-indicator-container">
                                <img src="{{url('vendor/subsystem/images/user.png')}}"
                                     class="w-32px h-32px rounded-pill"
                                     alt="">
                                <span class="status-indicator bg-success"></span>
                            </div>
                            <span class="d-none d-lg-inline-block mx-lg-2">
                                {{auth()->user()?->firstName . ' ' . auth()->user()?->lastName}}
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{route('logout')}}" class="dropdown-item">
                                <i class="ph-sign-out  me-2"></i>
                                {{st('logout')}}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /main navbar -->

        <!-- Inner content -->
        <div class="content-inner">
            <!-- Page header -->
            <div class="page-header">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        @yield('pageHeader')
                    </div>
                </div>
            </div>
            <!-- /page header -->

            <!-- Content area -->
            <div class="content">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <h4 class="m-0">
                                    @yield('pageTitle')
                                    @if(view()->hasSection('pageSubTitle'))
                                        <span class="fw-lighter fs-6">« @yield('pageSubTitle') »</span>
                                    @endif
                                </h4>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                    @include('subsystem::layouts.messages')
                </div>
                @yield('content')
            </div>
            <!-- /content area -->

            <!-- Footer -->
            <div class="navbar navbar-sm navbar-footer border-top">
                <div class="container-fluid">
                    <ul class="nav">
                        <li class="nav-item ms-md-1">
                            <div class="d-flex align-items-lg-end mx-md-1">
                                <i class="ph-book-open"></i>
                                <span class="d-none d-md-inline-block ms-2"></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /footer -->

        </div>
        <!-- /inner content -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->

@stack('js')
@yield('js')
</body>
</html>
