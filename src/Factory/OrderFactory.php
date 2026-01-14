<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Orders\Model\Order;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Security\Member;
use SilverStripe\View\ArrayData;

/**
 * Factory for creating/updating Order records from Foxy transaction data.
 */
class OrderFactory extends FoxyFactory
{
    use Configurable;
    use Extensible;
    use Injectable;

    private ?Order $order = null;

    /**
     * Return the Order object from a given transaction data set.
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getOrder(): Order
    {
        if (!$this->order instanceof Order) {
            $this->setOrder();
        }

        return $this->order;
    }

    /**
     * Find and update, or create new Order record and set.
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function setOrder(): static
    {
        /** @var ArrayData $transaction */
        $transaction = $this->getTransaction()->getParsedTransactionData()->getField('transaction');

        /** @var $order Order */
        if (
            $transaction->hasField('id')
            && !($order = Order::get()->filter('OrderID', $transaction->getField('id'))->first())
        ) {
            $order = Order::create();
        }

        if ($order->exists()) {
            $this->cleanRelatedOrderData($order);
        }

        foreach ($this->config()->get('order_mapping') as $foxy => $ssFoxy) {
            if ($transaction->hasField($foxy)) {
                $order->{$ssFoxy} = $transaction->getField($foxy);
            }
        }

        if ($member = Member::get()->filter('Email', $order->Email)->first()) {
            $order->MemberID = $member->ID;
        }

        $order->Response = urlencode($this->getTransaction()->getEncryptedData());

        $order->write();

        $order->Details()->addMany(OrderDetailFactory::create($this->getTransaction())->getOrderDetails());

        $this->order = Order::get()->byID($order->ID);

        return $this;
    }

    private function cleanRelatedOrderData(Order $order): void
    {
        $order->Details()->removeAll();
    }
}
