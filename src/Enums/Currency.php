<?php

namespace Alyani\Subsystem\Enums;

use Alyani\Subsystem\Enums\Traits\EnumUtils;

enum Currency: string
{
    use EnumUtils;

    case IRR = 'IRR';
    case USD = 'USD';
    case EUR = 'EUR';

    /**
     * Get the currency to display
     * example IRR => IRT
     */
    public function display(): string
    {
        $availableCurrencies = static::getCurrenciesForDisplay();
        return $availableCurrencies[$this->value] ?? $this->value;
    }

    public function displayTranslate(): string
    {
        return static::getTranslateFormat($this->display());
    }

    /**
     * Return all the values in an array.
     * if $withDisplayValues is true display values like IRT will be included
     *
     * @param boolean $withDisplayValues
     * @return array
     */
    public static function values($withDisplayValues = false): array
    {
        $availableCurrencies = static::getAvailableCurrencies();
        if ($withDisplayValues) {
            return
                array_unique(
                    array_merge(
                        $availableCurrencies,
                        static::displayValues()
                    )
                );
        }
        return $availableCurrencies;
    }

    /**
     * Get currencises with their display values
     * example IRR will be replaced with IRT
     */
    public static function displayValues(): array
    {
        return array_values(static::getCurrenciesForDisplay());
    }

     /**
     * Translate all the values and return localized
     * values.
     *
     * @return array
     */
    public static function displayValuesTranslate(): array
    {
        $translatedValues = [];
        foreach (static::displayValues() as $value) {
            $translatedValues[$value] = static::getTranslateFormat($value);
        }
        return $translatedValues;
    }

    /**
     * Get the oiginal currency
     * example if IRT is given , will return IRR
     */
    public static function original($value): string
    {
        $availableCurrencies = array_flip(static::getCurrenciesForDisplay());
        return $availableCurrencies[$value] ?? $value;
    }

    private static function getAvailableCurrencies(): array
    {
        $values = array_column(static::cases(), 'value');
        $availableCurrencies = config('subsystem.finance.currencies', ['IRR']);
        return array_intersect($values, $availableCurrencies);
    }

    private static function getCurrenciesForDisplay(): array
    {
        $currencyToDisplay = [];
        $availableCurrenciesDisplay = config('subsystem.finance.currenciesDisplay', ['IRR' => 'IRT']);
        $availableCurrencies = static::getAvailableCurrencies();
        foreach ($availableCurrencies as $currency) {
            $currencyToDisplay[$currency] = $availableCurrenciesDisplay[$currency] ?? $currency;
        }
        return $currencyToDisplay;
    }
}
