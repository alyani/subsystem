<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Alyani\Subsystem\Enums\UserStatus;
use Alyani\Subsystem\Http\Requests\Api\Profile\ChangePasswordRequest;
use Alyani\Subsystem\Http\Requests\Api\Profile\SetMobileRequest;
use Alyani\Subsystem\Http\Requests\Api\Profile\SetPasswordRequest;
use Alyani\Subsystem\Http\Requests\Api\Profile\SetRequest;
use Alyani\Subsystem\Http\Requests\Api\Profile\VerifyMobileRequest;
use Alyani\Subsystem\Http\Resources\UserResource;
use Alyani\Subsystem\Models\Storage;
use Alyani\Subsystem\Services\OtpSenders\SmsOtpSender;
use Alyani\Subsystem\Services\OTPService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function get()
    {
        $user = auth()->user();

        return $this->success([
            'user' => UserResource::make($user),
        ]);
    }

    public function set(SetRequest $request)
    {
        $authUser = auth()->user();
        $data = $request->validated();

        $authUser->name = $data['name'];
        $authUser->family = $data['family'] ?? '';
        $authUser->nickname = $data['nickname'] ?? '';
        $authUser->avatarSID = $data['avatarSID'] ?? '';

        if ($authUser->isDirty('avatarSID')) {
            if ($authUser->avatarSID) {
                try {
                    $storage = Storage::validateBeforeUse([
                        'SID' => $authUser->avatarSID,
                        'uploader_user_id' => $authUser->id
                    ]);
                    $storage->used($authUser, true);
                } catch (Exception $e) {
                    return $this->error(1, $e->getMessage());
                }
            }

            Storage::deleteBySID($authUser->getOriginal('avatarSID'));
        }
        $authUser->save();

        return $this->success([
            'user' => UserResource::make($authUser),
        ]);
    }

    public function setPassword(SetPasswordRequest $request)
    {
        $authUser = auth()->user();
        $data = $request->validated();

        if (!empty($authUser->password)) {
            return $this->error(1, st('The user has a password, please use this password for login'));
        }

        $authUser->password = Hash::make($data['password']);
        $authUser->save();

        return $this->success([
            'nextState' => $authUser->status == UserStatus::WaitingForSetProfile ? 'setProfile' : 'dashboard',
        ]);
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $authUser = Auth::user();

        if (!Hash::check($data['oldPassword'], $authUser->password)) {
            return $this->error(2, st('Current password is incorrect'));
        }

        $authUser->password = Hash::make($data['newPassword']);
        $authUser->update();

        return $this->success([
            'message' => st('Password changed successfully.'),
        ]);
    }

    public function setMobile(SetMobileRequest $request)
    {
        $authUser = auth()->user();
        $data = $request->validated();

        if (!empty($authUser->mobile)) {
            return $this->error(1, st('your mobile has been set before'));
        }

        try {
            $otp = new OTPService(
                action: 'setMobile',
                authorityKey: 'mobile',
                authorityValue: $data['mobile'],
                sender: app(SmsOtpSender::class),
                extraData: $data,
            );
            $otp->send();
        } catch (Exception $e) {
            if ($e->getCode() == OTPService::SEND_VERIFY_EXCEEDED) {
                return $this->error(2, $e->getMessage());
            }
            if ($e->getCode() == OTPService::SEND_RETRY_EXCEEDED) {
                return $this->error(3, $e->getMessage());
            }
            Log::error(__METHOD__ . ': Send OTP failed', [
                'target' => $data['mobile'],
                'method' => $otp?->data('method'),
                'error' => $e->getMessage(),
            ]);
            return $this->error(4, st('Failed to send verification code. Please try again.'));
        }

        return $this->success([
            'sentOTPDescription' => $otp->getSentPublicMessage(),
        ]);
    }

    public function verifyMobile(VerifyMobileRequest $request)
    {
        $authUser = auth()->user();
        $data = $request->validated();

        try {
            $otp = new OTPService(
                action: 'setMobile',
                authorityKey: 'mobile',
                authorityValue: $data['mobile'],
                sender: app(SmsOtpSender::class),
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
                'target' => $data['mobile'],
                'method' => $otp?->data('method'),
                'error' => $e->getMessage(),
            ]);
            return $this->error(4, st('Something went wrong. Please try again.'));
        }

        if (!empty($authUser->mobile)) {
            return $this->error(2, st('your mobile has been set before'));
        }

        $OTPData = $otp->data('extraData');
        $authUser->country_code = $OTPData['country_code'];
        $authUser->mobile = $OTPData['mobile'];
        $authUser->mobile_verified_at = now();
        $authUser->save();

        return $this->success();
    }
}
