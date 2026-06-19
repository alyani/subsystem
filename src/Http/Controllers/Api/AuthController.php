<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Alyani\Subsystem\Enums\UserStatus;
use Alyani\Subsystem\Http\Requests\Api\Auth\LoginRequest;
use Alyani\Subsystem\Http\Requests\Api\Auth\RegisterRequest;
use Alyani\Subsystem\Http\Requests\Api\Auth\SendOTPRequest;
use Alyani\Subsystem\Services\OtpSenders\EmailOtpSender;
use Alyani\Subsystem\Services\OtpSenders\SmsOtpSender;
use Alyani\Subsystem\Services\OTPService;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function sendOTP(SendOTPRequest $request)
    {
        $data = $request->validated();
        $authorityKey = config('subsystem.signupAuthorityKey');
        $authorityValue = $data[$authorityKey];

        $user = User::where($authorityKey, $authorityValue)->first();
        if ($user && $data['action'] === 'register') {
            return $this->error(1, st('Account already exists. Please use your password to login.'));
        }
        if (!$user && $data['action'] === 'resetPassword') {
            return $this->error(2, st('No account found with these details.'));
        }

        try {
            $sender = $authorityKey === 'email'
                ? app(EmailOtpSender::class)
                : app(SmsOtpSender::class);
            $otp = new OTPService(
                action: $data['action'],
                authorityKey: $authorityKey,
                authorityValue: $authorityValue,
                sender: $sender,
                extraData: $data,
            );
            $otp->send();
        } catch (Exception $e) {
            if ($e->getCode() == OTPService::SEND_VERIFY_EXCEEDED) {
                return $this->error(3, $e->getMessage());
            }
            if ($e->getCode() == OTPService::SEND_RETRY_EXCEEDED) {
                return $this->error(4, $e->getMessage());
            }
            Log::error(__METHOD__ . ': Send OTP failed', [
                'target' => $authorityValue,
                'method' => $otp->data('method'),
                'error' => $e->getMessage(),
            ]);
            return $this->error(5, st('Failed to send verification code. Please try again.'));
        }

        return $this->success([
            'sentOTPDescription' => $otp->getSentPublicMessage(),
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $authorityKey = config('subsystem.signupAuthorityKey');
        $authorityValue = $data[$authorityKey];

        try {
            $sender = $authorityKey === 'email'
                ? app(EmailOtpSender::class)
                : app(SmsOtpSender::class);
            $otp = new OTPService(
                action: 'register',
                authorityKey: $authorityKey,
                authorityValue: $authorityValue,
                sender: $sender,
            );
            $otp->verify($data['OTP']);
        } catch (Exception $e) {
            if ($e->getCode() == OTPService::EXPIRED) {
                return $this->error(1, $e->getMessage());
            }
            if ($e->getCode() == OTPService::SEND_VERIFY_EXCEEDED) {
                return $this->error(2, $e->getMessage());
            }
            if ($e->getCode() == OTPService::INVALID_OTP) {
                return $this->error(3, $e->getMessage());
            }
            Log::error(__METHOD__ . ': Verify OTP failed', [
                'target' => $authorityValue,
                'method' => $otp->data('method'),
                'error' => $e->getMessage(),
            ]);
            return $this->error(4, st('Something went wrong. Please try again.'));
        }

        $userOTPInfo = $otp->data('extraData');
        $user = User::where($authorityKey, $authorityValue)->first();
        if ($user) {
            return $this->error(5, st('Account already exists. Please use your password to login.'));
        }

        // Register new user
        $refereeUser = null;
        if (isset($userOTPInfo['referral_code']) && $userOTPInfo['referral_code']) {
            $refereeUser = User::where('referral_code', $userOTPInfo['referral_code'])->first();
            if ($refereeUser && $refereeUser->status == UserStatus::Banned) {
                $refereeUser = null;
            }
        }
        $user = new User();
        $user->forceFill([
            'referee_user_id' => $refereeUser->id ?? null,
            'referral_code' => User::generateReferralCode(),
            'referred_users_count' => 0,
            'mobile' => $userOTPInfo['mobile'] ?? null,
            'country_code' => $userOTPInfo['country_code'] ?? null,
            'email' => $userOTPInfo['email'] ?? null,
            'name' => $userOTPInfo['name'] ?? null,
            'family' => $userOTPInfo['family'] ?? null,
            'nickname' => $userOTPInfo['nickname'] ?? null,
            'password' => Hash::make($userOTPInfo['password']),
            'status' => UserStatus::Active,
            'last_activity' => now(),
        ]);
        if ($authorityKey === 'email') {
            $user->email_verified_at = now();
        } else {
            $user->mobile_verified_at = now();
        }
        $user->save();
        if ($refereeUser) {
            $refereeUser->increment('referred_users_count');
        }

        return $this->success([
            'token' => $user->createToken('owner')->plainTextToken,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $authorityKey = config('subsystem.signupAuthorityKey');
        $authorityValue = $data[$authorityKey];

        $user = User::where($authorityKey, $authorityValue)->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(1, st('Invalid credentials. Please check your account details and password.'));
        }

        // check for suspended user
        if ($user->status === UserStatus::Banned) {
            return $this->error(2, st('Your account has been suspended. Please contact support for assistance.'));
        }

        if (Config::get('subsystem.singleToken', false)) {
            $user->tokens()->delete();
        }

        $user->last_activity = now();
        $user->save();

        return $this->success([
            'token' => $user->createToken('owner')->plainTextToken,
        ]);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->success();
    }
}
