<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\PaymentGatewayType;
use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Enums\UserStatus;
use Alyani\Subsystem\Enums\WithdrawalGatewayType;
use Alyani\Subsystem\Enums\WithdrawalStatus;
use App\Models\User;
use Alyani\Subsystem\Models\Payment;
use Alyani\Subsystem\Models\PaymentGateway;
use Alyani\Subsystem\Http\Requests\Admin\UserManageBalance\DecreaseRequest;
use Alyani\Subsystem\Http\Requests\Admin\UserManageBalance\IncreaseRequest;
use Alyani\Subsystem\Models\Withdrawal;
use Alyani\Subsystem\Models\WithdrawalGateway;
use Throwable;

class UserManageBalanceController extends Controller
{
    /**
     * @param User $user
     */
    public function manageBalance(User $user)
    {
        $type = trim(request('type'));

        return view('subsystem::admin.userManageBalance.manage_balance', [
            'user' => $user,
            'type' => in_array($type, ['increase', 'decrease']) ? $type : 'increase',
        ]);
    }

    /**
     * Increase user's balance manualy
     *
     * @param IncreaseRequest $request
     */
    public function increase(IncreaseRequest $request, User $user)
    {
        $data = $request->validated();

        // Check user
        if ($user->status == UserStatus::Banned) {
            return back()->with('error', st('user is not active'));
        }

        // Check payment gateway
        $paymentGateway = PaymentGateway::where('type', PaymentGatewayType::Manual)->first();
        if (!$paymentGateway) {
            return back()->with('error', st('manual gateway not found'));
        }
        if ($paymentGateway->status !== ActivationStatus::Active) {
            return back()->with('error', st('manual gateway is not active'));
        }

        // Exchange amount to originalCurrency (IRT to IRR)
        $originalCurrency = Currency::original($data['currency']);
        $data['amount'] = exchange($data['amount'], $data['currency'], $originalCurrency);
        $data['currency'] = $originalCurrency;

        $payment = new Payment();
        $payment->forceFill([
            'user_id' => $user->id,
            'manager_id' => auth()->id(),
            'payment_gateway_id' => $paymentGateway->id,
            'base_amount' => $data['amount'],
            'transaction_fee_amount' => 0,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'gateway_data' => ['description' => $data['description'] ?? ''],
            'ip' => getClientIP(),
            'status' => PaymentStatus::Pending,
        ]);

        try {
            $payment->save();
            $payment->setVerified();
        } catch (Throwable $e) {
            return back()->with('error', st('an error has occurred'));
        }
        return redirect()->route('admin.user.show', $user)->with('success', st('operation done successfully'));
    }

    /**
     * Decrease user's balance manualy
     *
     * @param DecreaseRequest $request
     */
    public function decrease(DecreaseRequest $request, User $user)
    {
        $data = $request->validated();

        // Exchange amount to originalCurrency (IRT to IRR)
        $originalCurrency = Currency::original($data['currency']);
        $data['amount'] = exchange($data['amount'], $data['currency'], $originalCurrency);
        $data['currency'] = $originalCurrency;

        if ($user->balance < $data['amount']) {
            return back()->with('error', st('user balance is not enough'));
        }

        // Check withdrawal gateway
        $withdrawalGateway = WithdrawalGateway::where('type', WithdrawalGatewayType::Manual)->first();
        if (!$withdrawalGateway) {
            return back()->with('error', st('manual gateway not found'));
        }
        if ($withdrawalGateway->status !== ActivationStatus::Active) {
            return back()->with('error', st('manual gateway is not active'));
        }

        $withdrawal = new Withdrawal();
        $withdrawal->forceFill([
            'user_id' => $user->id,
            'withdrawal_gateway_id' => $withdrawalGateway->id,
            'manager_id' => auth()->id(),
            'base_amount' => $data['amount'],
            'transaction_fee_amount' => 0,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'gateway_data' => ['description' => $data['description'] ?? ''],
            'ip' => getClientIP(),
            'status' => WithdrawalStatus::Pending,
        ]);
        try {
            $withdrawal->save();
            $withdrawal->setVerified();
        } catch (Throwable $e) {
            return back()->with('error', st('an error has occurred'));
        }
        return redirect()->route('admin.user.show', $user)->with('success', st('operation done successfully'));
    }
}
