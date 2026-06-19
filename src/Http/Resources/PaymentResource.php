<?php

namespace Alyani\Subsystem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payment_id' => $this->id,
            'base_amount' => exchange($this->base_amount, $this->currency, $this->currency->display()),
            'transaction_fee_amount' => exchange($this->transaction_fee_amount, $this->currency, $this->currency->display()),
            'amount' => exchange($this->amount, $this->currency, $this->currency->display()),
            'currency' => $this->currency->display(),
            'gateway_reference' => $this->gateway_reference,
            'payment_gateway' => $this->paymentGateway->title[config('app.locale')] ?? current($this->paymentGateway->title),
            'status' => $this->status->getTranslate(),
            'created_at' => toJalaliDate($this->created_at, 'Y/m/d H:i:s'),
            'payment_date' => toJalaliDate($this->payment_date, 'Y/m/d H:i:s'),
        ];
    }
}
