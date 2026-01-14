<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Parser\Foxy\Transaction;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * Base factory class for processing Foxy transaction data.
 */
class FoxyFactory
{
    use Configurable;
    use Injectable;

    private ?Transaction $transaction = null;

    public function __construct(?Transaction $transaction = null)
    {
        if ($transaction instanceof Transaction) {
            $this->setTransaction($transaction);
        }
    }

    public function setTransaction(Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    protected function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }
}
