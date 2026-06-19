<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function login()
    {
        return view('subsystem::admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function handleLogin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }

    /**
     * @return JsonResponse
     */
    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }
}
