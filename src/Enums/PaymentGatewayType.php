<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum PaymentGatewayType: string
{
    use EnumUtils;

    case Online = 'online';
    case Manual = 'manual';
}
