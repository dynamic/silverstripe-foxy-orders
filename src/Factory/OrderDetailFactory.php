<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Orders\Model\OrderDetail;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * Factory for creating OrderDetail records from Foxy product data.
 */
class OrderDetailFactory extends FoxyFactory
{
    private ?ArrayList $order_details = null;

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function setOrderDetails(): static
    {
        $details = ArrayList::create();

        /** @var ArrayList $products */
        $products = $this->getTransaction()->getParsedTransactionData()->products;

        /** @var ArrayData $detail */
        foreach ($products as $detail) {
            $orderDetail = OrderDetail::create();

            foreach ($this->config()->get('order_detail_mapping') as $foxy => $ssFoxy) {
                if ($detail->hasField($foxy)) {
                    $orderDetail->{$ssFoxy} = $detail->getField($foxy);
                }
            }

            $codeFilter = function (\Page $page) use ($detail) {
                return $page->Code == $detail->getField('product_code');
            };

            if ($product = FoxyHelper::singleton()->getProducts()->filterByCallback($codeFilter)->first()) {
                $orderDetail->ProductID = $product->ID;
            } elseif ($variation = Variation::get()->filter('FinalCode', $detail->getField('product_code'))->first()) {
                $orderDetail->ProductID = $variation->ProductID;
            }

            $orderDetail->write();

            $orderDetail->OrderVariations()->addMany(OrderVariationFactory::create(
                $detail,
                $orderDetail->ProductID
            )->getOrderVariations());

            $details->push($orderDetail);
        }

        $this->order_details = $details;

        return $this;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getOrderDetails(): ArrayList
    {
        if (!$this->order_details) {
            $this->setOrderDetails();
        }

        return $this->order_details;
    }
}
