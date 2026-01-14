<?php

namespace Dynamic\Foxy\Orders\Extension;

use Dynamic\Foxy\Orders\Model\Order;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Extends Member to add order history.
 */
class MemberDataExtension extends DataExtension
{
    private static array $has_many = [
        'Orders' => Order::class,
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->replaceField('Customer_ID', TextField::create('Customer_ID')->performReadonlyTransformation());
    }
}
