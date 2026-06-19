<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum PaymentStatus: string
{
    use EnumUtils;

    case Pending = 'pending';
    case Processing = 'processing';
    case Verified = 'verified';
    case Failed = 'failed';
}
