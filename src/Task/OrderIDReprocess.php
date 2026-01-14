<?php

namespace Dynamic\Foxy\Orders\Task;

use Dynamic\Foxy\Orders\Factory\OrderFactory;
use Dynamic\Foxy\Orders\Model\Order;
use Dynamic\Foxy\Parser\Foxy\Transaction;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Control\HTTPRequest;

/**
 * Task to reprocess orders with incorrect OrderID values.
 */
class OrderIDReprocess extends BuildTask
{
    protected $title = 'Reprocess Order ID';

    protected $description = 'Reprocess Orders to correct order ID';

    private static $segment = 'OrderIDReprocess';

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function run($request): void
    {
        foreach ($this->yieldOrders() as $order) {
            // Note: Stored responses are always XML format from legacy datafeed
            $transaction = Transaction::create(urldecode($order->Response));
            $reprocessedOrder = OrderFactory::create($transaction)->getOrder();

            echo 'Order ID: ' . $reprocessedOrder->OrderID . ' reprocessed' . PHP_EOL;

            echo 'Deleting legacy Order ID: ' . $order->ID . PHP_EOL;
            $order->delete();
        }
    }

    protected function yieldOrders(): \Generator
    {
        foreach (Order::get()->filter('OrderID', 2147483647) as $order) {
            yield $order;
        }
    }
}
