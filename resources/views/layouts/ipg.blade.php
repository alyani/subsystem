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
    <link rel="stylesheet" href="{{url('vendor/subsystem/css/custom.css')}}" id="stylesheet" type="text/css">

    <!-- Theme JS files -->
    <script src="{{url('vendor/subsystem/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{url('vendor/subsystem/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('vendor/subsystem/plugins/datatables-responsive/js/responsive.bootstrap4.js')}}"></script>
    <script src="{{url('vendor/subsystem/js/custom.js')}}"></script>
    <script src="{{url('vendor/subsystem/js/app.js')}}"></script>
    <!-- /theme JS files -->

    <style>
        body {
            min-height: 100vh;
            background: #212529;
            color: #dee2e6;
        }

        .callback-card {
            max-width: 480px;
            width: 100%;
            background: #2b3035;
            border: 1px solid #495057;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        }

        .callback-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: rgba(255, 255, 255, .08);
        }

        .callback-message {
            color: #adb5bd;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center p-3">

    <!-- Main content -->
    @yield('content')
    <!-- /main content -->

    @stack('js')
    @yield('js')
</body>
</html>
