<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum ManagerStatus: string
{
    use EnumUtils;

    case Active = 'active';
    case Banned = 'banned';
}
