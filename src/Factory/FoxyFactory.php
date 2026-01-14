<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Parser\Foxy\TransactionInterface;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * Base factory class for processing Foxy transaction data.
 */
class FoxyFactory
{
    use Configurable;
    use Injectable;

    private ?TransactionInterface $transaction = null;

    public function __construct(?TransactionInterface $transaction = null)
    {
        if ($transaction instanceof TransactionInterface) {
            $this->setTransaction($transaction);
        }
    }

    public function setTransaction(TransactionInterface $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    protected function getTransaction(): ?TransactionInterface
    {
        return $this->transaction;
    }
}
