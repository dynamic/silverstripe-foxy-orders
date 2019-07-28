<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Orders\Foxy\Transaction;
use Dynamic\Foxy\Orders\Model\OrderDetail;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;

/**
 * Class OrderDetailFactory
 * @package Dynamic\Foxy\Orders\Factory
 */
class OrderDetailFactory
{
    use Configurable;
    use Injectable;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var ArrayList
     */
    private $order_details;

    /**
     * OrderDetailFactory constructor.
     * @param Transaction|null $transaction
     */
    public function __construct(Transaction $transaction = null)
    {
        if ($transaction instanceof Transaction && $transaction !== null) {
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
     * @return $this
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function setOrderDetails()
    {
        $details = ArrayList::create();

        $products = $this->getTransaction()->getParsedTransactionData()->products;

        foreach ($products as $detail) {
            $orderDetail = OrderDetail::create();

            foreach ($this->config()->get('order_detail_mapping') as $foxy => $ssFoxy) {
                $orderDetail->{$ssFoxy} = $detail->{$foxy};
            }

            $orderDetail->write();

            $details->push($orderDetail);
        }

        $this->order_details = $details;

        return $this;
    }

    /**
     * @return ArrayList
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getOrderDetails()
    {
        if (!$this->order_details) {
            $this->setOrderDetails();
        }

        return $this->order_details;
    }
}
