<?php

namespace Dynamic\Foxy\Orders\Model;

use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Represents a Foxy.io order imported from the data feed.
 */
class Order extends DataObject implements PermissionProvider
{
    private static array $db = [
        'StoreID' => 'Int',
        'OrderID' => 'BigInt',
        'Email' => 'Varchar(255)',
        'TransactionDate' => 'DBDatetime',
        'ProductTotal' => 'Currency',
        'TaxTotal' => 'Currency',
        'ShippingTotal' => 'Currency',
        'OrderTotal' => 'Currency',
        'ReceiptURL' => 'Varchar(255)',
        'OrderStatus' => 'Varchar(255)',
        'Response' => 'Text',
        'CustomerID' => 'Int',
    ];

    private static array $has_one = [
        'Member' => Member::class,
    ];

    private static array $has_many = [
        'Details' => OrderDetail::class,
    ];

    private static string $singular_name = 'Order';

    private static string $plural_name = 'Orders';

    private static string $description = 'Orders from FoxyCart Datafeed';

    private static string $default_sort = 'TransactionDate DESC, ID DESC';

    private static array $summary_fields = [
        'OrderID',
        'TransactionDate.Nice',
        'Email',
        'ProductTotal.Nice',
        'ShippingTotal.Nice',
        'TaxTotal.Nice',
        'OrderTotal.Nice',
        'ReceiptLink',
    ];

    private static array $searchable_fields = [
        'OrderID',
        'TransactionDate' => [
            'field' => DateField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Email',
        'OrderTotal',
    ];

    private static array $casting = [
        'ReceiptLink' => 'HTMLVarchar',
    ];

    private static array $indexes = [
        'OrderID' => true,
    ];

    private static string $table_name = 'FoxyOrder';

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['StoreID'] = _t(__CLASS__ . '.StoreID', 'Store ID#');
        $labels['OrderID'] = _t(__CLASS__ . '.OrderID', 'Order ID#');
        $labels['TransactionDate'] = _t(__CLASS__ . '.TransactionDate', 'Date');
        $labels['TransactionDate.NiceUS'] = _t(__CLASS__ . '.TransactionDate', 'Date');
        $labels['Email'] = _t(__CLASS__ . '.Email', 'Email');
        $labels['ProductTotal.Nice'] = _t(__CLASS__ . '.ProductTotal', 'Sub Total');
        $labels['TaxTotal.Nice'] = _t(__CLASS__ . '.TaxTotal', 'Tax');
        $labels['ShippingTotal.Nice'] = _t(__CLASS__ . '.ShippingTotal', 'Shipping');
        $labels['OrderTotal'] = _t(__CLASS__ . '.OrderTotal', 'Total');
        $labels['OrderTotal.Nice'] = _t(__CLASS__ . '.OrderTotal', 'Total');
        $labels['ReceiptLink'] = _t(__CLASS__ . '.ReceiptLink', 'Invoice');
        $labels['Details.ProductID'] = _t(__CLASS__ . '.Details.ProductID', 'Product');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName(['Response']);
        });

        return parent::getCMSFields();
    }

    public function ReceiptLink(): DBHTMLVarchar
    {
        return $this->getReceiptLink();
    }

    public function getReceiptLink(): DBHTMLVarchar
    {
        $obj = DBHTMLVarchar::create();
        $obj->setValue(
            '<a href="' . $this->ReceiptURL . '" target="_blank" class="cms-panel-link action external-link">view</a>'
        );

        return $obj;
    }

    public function providePermissions(): array
    {
        return [
            'MANAGE_FOXY_ORDERS' => [
                'name' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_ORDERS_DESCRIPTION',
                    'Manage orders'
                ),
                'category' => _t(
                    Purchasable::class . '.PERMISSIONS_CATEGORY',
                    'Foxy'
                ),
                'help' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_ORDERS_HELP',
                    'Manage orders and view recipts'
                ),
                'sort' => 400,
            ],
        ];
    }

    public function canView($member = null): bool
    {
        return Permission::checkMember($member, 'MANAGE_FOXY_ORDERS');
    }

    public function canEdit($member = null): bool
    {
        return false;
    }

    public function canDelete($member = null): bool
    {
        return false;
    }

    public function canCreate($member = null, $context = []): bool
    {
        return false;
    }
}
