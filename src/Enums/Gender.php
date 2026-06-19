<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum Gender: string
{
    use EnumUtils;

    case Female = 'female';
    case Male = 'male';
}
