<?php

namespace Dynamic\Foxy\Orders\Factory;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Orders\Model\OrderVariation;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * Factory for creating OrderVariation records from Foxy product variation data.
 */
class OrderVariationFactory
{
    use Configurable;
    use Injectable;

    private ?ArrayList $order_variations = null;

    private ?ArrayData $foxy_product = null;

    private mixed $product = null;

    public function __construct(?ArrayData $foxyProduct = null, int $productID = 0)
    {
        if ($foxyProduct instanceof ArrayData) {
            $this->setFoxyProduct($foxyProduct);
            $this->setProduct($productID);
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

    public function setProduct(int $productID): static
    {
        $this->product = FoxyHelper::singleton()->getProducts()->filter('ID', $productID)->first();

        return $this;
    }

    protected function getProduct(): mixed
    {
        return $this->product;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function setOrderVariations(): static
    {
        $variations = ArrayList::create();

        foreach ($this->getFoxyProduct()->transaction_detail_options as $variationItem) {
            $variation = OrderVariation::create();

            foreach ($this->config()->get('variation_mapping') as $foxyField => $ssField) {
                if ($variationItem->hasField($foxyField)) {
                    $variation->{$ssField} = $variationItem->getField($foxyField);
                }
            }

            if ($this->getProduct()) {
                $productVariation = Variation::get()->filter([
                    'Title' => $variation->Value,
                    'ProductID' => $this->getProduct()->ID,
                ])->first();

                if ($productVariation) {
                    $variation->VariationID = $productVariation->ID;
                }
            }

            $variation->write();
            $variations->push($variation);
        }

        $this->order_variations = $variations;

        return $this;
    }

    public function getOrderVariations(): ArrayList
    {
        if (!$this->order_variations instanceof ArrayList) {
            $this->setOrderVariations();
        }

        return $this->order_variations;
    }
}
