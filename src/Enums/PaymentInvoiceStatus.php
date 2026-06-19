<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum PaymentInvoiceStatus: string
{
    use EnumUtils;

    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case PaidUncompleted = 'paid_uncompleted';
    case Failed = 'failed';
}
