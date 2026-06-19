<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Auth;

use Alyani\Subsystem\Enums\ManagerStatus;
use Illuminate\Auth\Events\Lockout;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => ['required', 'string', 'validMobile'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'captcha'],
        ];
    }

    protected function prepareForValidation()
    {
        $mobile = $this->mobile;
        if (!empty($mobile)) {
            $mobile = replacePersianDigistWithEnglish(ltrim($mobile, 0));
            if ($mobile) {
                $mobile = normalizeMobile($mobile);
            }
        }
        $this->merge([
            'mobile' => replacePersianDigistWithEnglish($mobile),
            'captcha' => replacePersianDigistWithEnglish($this->captcha),
            'password' => replacePersianDigistWithEnglish($this->password),
        ]);
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();
        if (!Auth::attempt($this->only('mobile', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'mobile' => st('authFailed'),
            ]);
        }
        if (auth()->user()->status !== ManagerStatus::Active->value) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'mobile' => st('Your account has been suspended. Please contact support for assistance.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'mobile' => st('authThrottle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->input('mobile')) . '|' . $this->ip());
    }
}
