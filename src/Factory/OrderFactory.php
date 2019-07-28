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
     * @var string The encrypted Foxy.io xml data feed response.
     */
    private $encrypted_order_data;

    /**
     * @var Order
     */
    private $order;

    /**
     * OrderFactory constructor.
     * @param null $encryptedOrderData
     */
    public function __construct($encryptedOrderData = null)
    {
        if ($encryptedOrderData !== null) {
            $this->setEncryptedOrderData($encryptedOrderData);
        }
    }

    /**
     * Set the encrypted Foxy.io xml data feed response.
     *
     * @param $encryptedData
     * @return $this
     */
    public function setEncryptedOrderData($encryptedData)
    {
        $this->encrypted_order_data = $encryptedData;

        return $this;
    }

    /**
     * Return the encrypted Foxy.io xml data feed response.
     *
     * @return string
     */
    protected function getEncryptedOrderData()
    {
        return $this->encrypted_order_data;
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
        $transaction = Transaction::create($this->getEncryptedOrderData())->getParsedTransactionData();

        $order = (Order::get()->filter('OrderID', $transaction->transaction->id)->first())
            ?: Order::create();

        foreach ($this->config()->get('order_mapping') as $foxy => $ssFoxy) {
            $order->{$ssFoxy} = $transaction->transaction->{$foxy};
        }

        $order->write();

        $this->order = $order;

        return $this;
    }
}
