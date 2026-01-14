<?php

namespace Dynamic\Foxy\Orders\Extension;

use Dynamic\Foxy\Orders\Factory\OrderFactory;
use Dynamic\Foxy\Parser\Foxy\TransactionInterface;
use SilverStripe\Core\Extension;

/**
 * Extension to process orders from Foxy transaction data.
 */
class FoxyControllerOrderExtension extends Extension
{
    /**
     * Process incoming transaction data and create/update orders.
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function doAdditionalParse(TransactionInterface $transaction): void
    {
        OrderFactory::create($transaction)->getOrder();
    }
}
