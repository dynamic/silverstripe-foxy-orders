<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Orders\Foxy\Transaction;
use Dynamic\Foxy\Orders\Model\Order;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;

/**
 * Class OrderFactory
 * @package Dynamic\Foxy\Orders\Factory
 */
class OrderFactory
{
    use Configurable;
    use Extensible;
    use Injectable;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Order
     */
    private $order;

    /**
     * OrderFactory constructor.
     * @param Transaction|null $transaction
     */
    public function __construct(Transaction $transaction = null)
    {
        if ($transaction !== null && $transaction instanceof Transaction) {
            $this->setTransaction($transaction);
        }
    }

    /**
     * @param Transaction $transaction
     * @return $this
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return Transaction
     */
    protected function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Return the Order object from a given transaction data set.
     *
     * @return Order
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getOrder()
    {
        if (!$this->order instanceof Order) {
            $this->setOrder();
        }

        return $this->order;
    }

    /**
     * Find and update, or create new Order record and set.
     *
     * @return $this
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function setOrder()
    {
        $transaction = $this->getTransaction()->getParsedTransactionData();

        $order = (Order::get()->filter('OrderID', $transaction->transaction->id)->first())
            ?: Order::create();

        if ($order->exists()) {
            $this->cleanRelatedOrderData($order);
        }

        foreach ($this->config()->get('order_mapping') as $foxy => $ssFoxy) {
            $order->{$ssFoxy} = $transaction->transaction->{$foxy};
        }

        $order->write();

        $order->Details()->addMany(OrderDetailFactory::create($this->getTransaction())->getOrderDetails());

        $this->order = Order::get()->byID($order->ID);

        return $this;
    }

    /**
     * @param Order $order
     */
    private function cleanRelatedOrderData(Order $order)
    {
        $order->Details()->removeAll();
    }
}
