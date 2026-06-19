<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum Language: string
{
    use EnumUtils;

    case Fa = 'fa';
    case En = 'en';
    case Ar = 'ar';

    /**
     * Return all the values in an array.
     *
     * @return array
     */
    public static function values(): array
    {
        $values = array_column(static::cases(), 'value');
        $availableLocales = config('app.locales') ?: [
            config('app.locale'),
            config('app.fallback_locale'),
        ];
        return array_intersect($values, $availableLocales) ;
    }
}
