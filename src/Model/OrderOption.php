<?php

namespace Dynamic\Foxy\Orders\Model;

use Dynamic\Foxy\Model\Variation;
use SilverStripe\ORM\DataObject;

/**
 * Represents a product variation selected for an order detail item.
 */
class OrderVariation extends DataObject
{
    private static array $db = [
        'Name' => 'Varchar(200)',
        'Value' => 'Varchar(200)',
    ];

    private static array $has_one = [
        'OrderDetail' => OrderDetail::class,
        'Variation' => Variation::class,
    ];

    private static array $summary_fields = [
        'Name',
        'Value',
    ];

    private static string $table_name = 'FoxyOrderVariation';
}
