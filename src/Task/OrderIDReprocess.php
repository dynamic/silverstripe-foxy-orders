<?php

namespace Dynamic\Foxy\Orders\Task;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Orders\Factory\OrderFactory;
use Dynamic\Foxy\Orders\Model\Order;
use Dynamic\Foxy\Parser\Foxy\Transaction;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\BuildTask;

class OrderIDReprocess extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Reprocess Order ID';

    /**
     * @var string
     */
    protected $description = 'Reprocess Orders to correct order ID';

    /**
     * @var string
     */
    private static $segment = 'OrderIDReprocess';

    /**
     * @param $request
     * @return void
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function run($request)
    {
        foreach ($this->yieldOrders() as $order) {
            $transaction = Transaction::create(urldecode($order->Response));
            $reprocessedOrder = OrderFactory::create($transaction)->getOrder();

            echo 'Order ID: ' . $reprocessedOrder->OrderID . ' reprocessed' . PHP_EOL;

            echo 'Deleting legacy Order ID: ' . $order->ID . PHP_EOL;
            $order->delete();

        }
    }

    /**
     * @return \Generator
     */
    protected function yieldOrders()
    {
        foreach (Order::get()->filter('OrderID', 2147483647) as $order) {
            yield $order;
        }
    }
}
