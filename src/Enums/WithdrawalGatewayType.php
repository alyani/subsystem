<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum WithdrawalGatewayType: string
{
    use EnumUtils;

    case Manual = 'manual';
}
