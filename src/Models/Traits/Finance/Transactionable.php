<?php

namespace Alyani\Subsystem\Models\Traits\Finance;

use Alyani\Subsystem\Contracts\Finance\TransactionableContract;
use Alyani\Subsystem\Enums\Currency;

/**
 * Add this trait to your model
 */
trait Transactionable
{
    public function getPayableBaseAmount(): int
    {
        return (int) ($this->base_amount ?? $this->amount);
    }

    public function getPayableVATAmount(): int
    {
        return (int) ($this->VAT_amount ?? 0);
    }

    public function getPayableAmount(): int
    {
        return $this->getPayableBaseAmount() + $this->getPayableVATAmount();
    }

    public function getPayableCurrency(): Currency
    {
        return $this->currency;
    }

    public static function getPayableTranslate(): string
    {
        $className = class_basename(get_called_class());
        return st($className);
    }

    public static function getPayableDetailAdminRoute(int $id = null)
    {
        return false;
    }
}
