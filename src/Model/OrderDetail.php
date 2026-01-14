<?php

namespace Dynamic\Foxy\Orders\Model;

use Dynamic\Foxy\Model\Variation;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;

/**
 * Represents a line item within a Foxy order.
 */
class OrderDetail extends DataObject
{
    private static array $db = [
        'Quantity' => 'Int',
        'Price' => 'Currency',
        'ProductName' => 'HTMLVarchar(255)',
        'ProductCode' => 'Varchar(100)',
        'ProductImage' => 'Text',
        'ProductCategory' => 'Varchar(100)',
    ];

    private static array $has_one = [
        'Product' => SiteTree::class,
        'Order' => Order::class,
    ];

    private static array $has_many = [
        'OrderOptions' => OrderOption::class,
        'OrderVariations' => OrderVariation::class,
    ];

    private static array $summary_fields = [
        'Product.Title',
        'Quantity',
        'Price.Nice',
    ];

    private static array $indexes = [
        'ProductCode' => true,
    ];

    private static string $table_name = 'FoxyOrderDetail';
}
