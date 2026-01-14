<?php

namespace Dynamic\Foxy\Orders\Model;

use SilverStripe\ORM\DataObject;

/**
 * Represents an option selected for a product in an order detail.
 */
class OrderOption extends DataObject
{
    private static array $db = [
        'Name' => 'Varchar(200)',
        'Value' => 'Varchar(200)',
    ];

    private static array $has_one = [
        'OrderDetail' => OrderDetail::class,
    ];

    private static array $summary_fields = [
        'Name',
        'Value',
    ];

    private static string $table_name = 'FoxyOrderOption';
}
