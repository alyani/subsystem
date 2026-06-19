<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }}</title>

    <link href="{{ url('vendor/subsystem/fonts/vazir/font-face.css') }}" rel="stylesheet">
    <link href="{{ url('vendor/subsystem/icons/phosphor/styles.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendor/subsystem/css/rtl/all.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendor/subsystem/css/rtl/style.css') }}" rel="stylesheet">

    <script src="{{ url('vendor/subsystem/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('vendor/subsystem/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('vendor/subsystem/js/app.js') }}"></script>

    <style>
        body {
            background: url('{{ url("vendor/subsystem/images/backgroundLogin.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'vazir';
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            max-width: 420px;
            width: 100%;
            padding: 2rem;
            color: #fff;
        }

        .glass-card h5 {
            color: #212121;
        }

        .form-label {
            color: #212121;
            font-weight: 500;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.5);
            color: #212121;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: background-color 0.3s, color 0.3s;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3);
            color: #212121;
            outline: none;
            border-color: #60a5fa;
        }

        .form-control::placeholder {
            color: #e5e7eb;
        }

        .form-control::placeholder {
            color: #212121;
        }

        .btn-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .captcha {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reload {
            padding: 0 12px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .form-control-feedback-icon {
            pointer-events: none;
        }

        .login-logo {
            height: 50px;
            margin-bottom: 1.2rem;
        }

        .glass-button {
            display: inline-block;
            padding: 24px 32px;
            border: 0;
            text-decoration: none;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            letter-spacing: 2px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .glass-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn-glassy-blue {
            background: rgba(79, 157, 252, 0.40); /* شفاف آبی */
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #313131;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 20px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease-in-out;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 0 12px rgba(79, 157, 252, 0.3), 0 4px 24px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-glassy-blue::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1), transparent 70%);
            transform: rotate(25deg);
            transition: all 0.6s ease;
        }

        .btn-glassy-blue:hover::before {
            top: -80%;
            left: -80%;
            opacity: 1;
        }

        .btn-glassy-blue:hover {
            background: rgba(79, 157, 252, 0.25);
            box-shadow: 0 0 20px rgba(79, 157, 252, 0.6), 0 6px 28px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .captcha-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .captcha-row span {
            cursor: pointer;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);

            background-color: rgba(255, 255, 255, 0.12);
            user-select: none;
        }

        .captcha-row input {-
            flex: 1;
            font-size: 1rem;
        }

        .password-input,
        .mobile-input{
            direction: ltr;
        }
    </style>
</head>
<body>

<form class="glass-card" action="{{ route('handleLogin') }}" method="POST">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            @foreach ($errors->all() as $error)
                <p> {{$error}}</p>
            @endforeach
        </div>
    @endif

    <div class="text-center">
        <img src="{{ url('vendor/subsystem/images/logo.png') }}" class="login-logo" alt="Logo">
        <h5 class="mb-3">{{ st('login to admin panel') }}</h5>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ st('enter your phone number') }}</label>
        <div class="form-control-feedback">
            <input type="text" class="form-control mobile-input" name="mobile" placeholder="">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ st('enter your password') }}</label>
        <div class="position-relative">
            <input type="password" class="form-control password-input" name="password" id="passwordInput" placeholder="">
            <i id="togglePassword"
               class="ph ph-eye text-black"
               style="position:absolute; top:50%; transform:translateY(-50%); right:10px; cursor:pointer;"></i>
        </div>
    </div>

    <div class="captcha-row">
        <span id="captcha-image">{!! captcha_img() !!}</span>
        <input type="text" name="captcha" id="captcha" class="form-control" placeholder="{{ st('enter captcha') }}"/>
    </div>

    <button type="submit" class="btn-glassy-blue w-100">
        {{ st('login') }}
    </button>

</form>

<script>
    function refreshCaptcha() {
        $.ajax({
            type: 'GET',
            url: '{{ route("reloadCaptcha") }}',
            success: function (data) {
                $("#captcha-image").html(data.captcha);
            }
        });
    }

    $(document).on('click', '#reload, #captcha-image', function (e) {
        e.preventDefault();
        refreshCaptcha();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById("passwordInput");
        const togglePassword = document.getElementById("togglePassword");

        togglePassword.addEventListener("click", function () {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            togglePassword.classList.toggle("ph-eye");
            togglePassword.classList.toggle("ph-eye-slash");
        });
    });
</script>

@stack('js')
@yield('js')

</body>
</html>
