<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum ActivationStatus: string
{
    use EnumUtils;

    case Active = 'active';
    case Inactive = 'inactive';
}
