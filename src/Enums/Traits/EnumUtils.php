<?php

namespace Alyani\Subsystem\Enums\Traits;

/**
 * Enum utils
 */
trait EnumUtils
{
    /**
     * Return all the names in an array.
     *
     * @return array
     */
    public static function names()
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * Return all the values in an array.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Search the Enum and return the case for
     * the given value.
     *
     * @param string $name
     * @return object
     */
    public static function tryFromCase($name)
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
    }

    /**
     * Translate all the values and return localized
     * values.
     *
     * @return array
     */
    public static function valuesTranslate(): array
    {
        $translatedValues = [];
        foreach (static::values() as $value) {
            $translatedValues[$value] = static::getTranslateFormat($value);
        }
        return $translatedValues;
    }

    /**
     * Translate Format
     *
     * @param $value
     * @return string
     */
    public static function getTranslateFormat($value): string
    {
        $className = class_basename(get_called_class());
        return st("{$className}.{$value}", [], 'enum');
    }

    public function getTranslate(): string
    {
        return static::getTranslateFormat($this->value);
    }
}
