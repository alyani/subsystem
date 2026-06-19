@php
    use Alyani\Subsystem\Enums\PaymentStatus;
@endphp
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            {{st('Payment Details')}}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Payment gateway')}}</p></div>
            <div class="col-sm-9 text-muted small text-start">
                {{ $payment->paymentGateway->title['fa']}}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Base amount')}}</p></div>
            <div class="col-sm-9 text-muted small text-start">
                {{ number_format($payment->base_amount_exchanged) }} {{ $payment->currency->displayTranslate() }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Transaction fee amount')}}</p></div>
            <div class="col-sm-9 text-muted small text-start">
                {{ number_format($payment->transaction_fee_amount_exchanged) }} {{ $payment->currency->displayTranslate() }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Final amount')}}</p></div>
            <div class="col-sm-9 text-muted small text-start">
                {{ number_format($payment->amount_exchanged) }} {{ $payment->currency->displayTranslate() }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Payment status')}}</p></div>
            <div class="col-sm-9 left-to-right text-start">
                <span class="text-muted left-to-right text-start">
                    @if($payment->status == PaymentStatus::Verified)
                        {{$payment->status->getTranslate()}}
                        <i class="fa fa-check-circle text-success ms-1"></i> 
                    @elseif($payment->status == PaymentStatus::Failed)
                        {{$payment->status->getTranslate()}}
                        <i class="fa fa-warning text-warning ms-1"></i> 
                    @else
                        {{$payment->status->getTranslate()}}
                    @endif
                </span>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-3"><p class="mb-0">{{st('Gateway reference')}}</p></div>
            <div class="col-sm-9 text-muted small left-to-right text-start">
                {{ $payment->gateway_reference}}
            </div>
        </div>
        <hr>

    </div>
</div>
