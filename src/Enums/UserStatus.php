<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum UserStatus: string
{
    use EnumUtils;

    case WaitingForSetProfile = 'waitingForSetProfile';
    case Active = 'active';
    case Banned = 'banned';
}
