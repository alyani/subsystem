<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum TransactionType: string
{
    use EnumUtils;

    case Increase = 'increase';
    case Decrease = 'decrease';
}
