<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Alyani\Subsystem\Http\Requests\Api\Payment\ListRequest;
use Alyani\Subsystem\Http\Resources\PaymentResource;
use Alyani\Subsystem\Models\Payment;

class PaymentController extends Controller
{
    /**
     * @param ListRequest $request
     * @link https://docs.google.com/document/d/1dTMbjeM45KJ55maDcCTAgyw5Vy-Y203ff12pghlBql4/edit?tab=t.0
     */
    public function list(ListRequest $request)
    {
        $data = $request->validated();

        $payments = Payment::query()
            ->with('paymentGateway')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->pageLimit($data['page'] ?? null, $data['items_per_page'] ?? null);

        return $this->success([
            'payments' => PaymentResource::collection($payments),
        ]);
    }
}
