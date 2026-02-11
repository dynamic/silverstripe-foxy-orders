<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Orders\Model\Order;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Security\Member;
use SilverStripe\View\ArrayData;

/**
 * Class OrderFactory
 * @package Dynamic\Foxy\Orders\Factory
 */
class OrderFactory extends FoxyFactory
{
    use Configurable;
    use Extensible;
    use Injectable;

    /**
     * @var Order
     */
    private $order;

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
        /** @var ArrayData $transaction */
        $transaction = $this->getTransaction()->getParsedTransactionData()->getField('transaction');

        /** @var $order Order */
        if ($transaction->hasField('id')
            && !($order = Order::get()->filter('OrderID', $transaction->getField('id'))->first())) {
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

        if ($order->Details()->count()) {
            foreach ($order->Details() as $detail) {
                if ($product = FoxyHelper::singleton()->getProducts()->filter('ID', $detail->ProductID)->first()) {
                    if ($product->hasMethod('getNumberPurchasedUpdate')) {
                        $product->NumberPurchased = $product->getNumberPurchasedUpdate();
                        $product->write();
                    }
                }
                if ($detail->OrderVariations()->count()) {
                    foreach ($detail->OrderVariations() as $orderVariation) {
                        if ($orderVariation->VariationID) {
                            if ($variaton = Variation::get()->filter('ID', $orderVariation->VariationID)->first()) {
                                if ($variaton->hasMethod('getNumberPurchasedUpdate')) {
                                    $variaton->NumberPurchased = $variaton->getNumberPurchasedUpdate();
                                    $variaton->write();
                                }
                            }
                        }
                    }
                }
            }
        }

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
