<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Orders\Model\OrderOption;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * Factory for creating OrderOption records from Foxy product option data.
 */
class OrderOptionFactory
{
    use Configurable;
    use Injectable;

    private ?ArrayList $order_options = null;

    private ?ArrayData $foxy_product = null;

    public function __construct(?ArrayData $foxyProduct = null)
    {
        if ($foxyProduct instanceof ArrayData) {
            $this->setFoxyProduct($foxyProduct);
        }
    }

    public function setFoxyProduct(ArrayData $foxyProduct): static
    {
        $this->foxy_product = $foxyProduct;

        return $this;
    }

    protected function getFoxyProduct(): ?ArrayData
    {
        return $this->foxy_product;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function setOrderOptions(): static
    {
        $options = ArrayList::create();

        /** @var ArrayData $optionItem */
        foreach ($this->getFoxyProduct()->transaction_detail_options as $optionItem) {
            $option = OrderOption::create();

            foreach ($this->config()->get('option_mapping') as $foxyField => $ssField) {
                if ($optionItem->hasField($foxyField)) {
                    $option->{$ssField} = $optionItem->getField($foxyField);
                }
            }

            $option->write();
            $options->push($option);
        }

        $this->order_options = $options;

        return $this;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function getOrderOptions(): ArrayList
    {
        if (!$this->order_options instanceof ArrayList) {
            $this->setOrderOptions();
        }

        return $this->order_options;
    }
}
