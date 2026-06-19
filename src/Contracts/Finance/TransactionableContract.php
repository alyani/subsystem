<?php

namespace Alyani\Subsystem\Contracts\Finance;

use Alyani\Subsystem\Enums\Currency;

interface TransactionableContract
{
    public function getPayableBaseAmount(): int;

    public function getPayableVATAmount(): int;

    public function getPayableAmount(): int;

    public function getPayableCurrency(): Currency;

    public static function getPayableTranslate(): string;

    public static function getPayableDetailAdminRoute(int $id = null);
}
