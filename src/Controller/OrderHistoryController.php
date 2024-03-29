<?php

namespace Dynamic\Foxy\Orders\Page;

use Dynamic\Foxy\Orders\Model\Order;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Security;

class OrderHistoryController extends \PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
    ];

    /**
     * @var PaginatedList
     */
    private $order_paginated_list;

    /**
     * @return bool|\SilverStripe\Control\HTTPResponse
     */
    public function checkMember()
    {
        if (Security::getCurrentUser()) {
            return true;
        } else {
            return Security::permissionFailure($this, _t(
                'AccountPage.CANNOTCONFIRMLOGGEDIN',
                'Please login to view this page.'
            ));
        }
    }

    /**
     * @return array
     */
    public function index(HTTPRequest $request)
    {
        $this->checkMember();

        return [];
    }

    protected function setOrderPaginatedList()
    {
        if (Security::getCurrentUser()) {
            $request = $this->getRequest();
            $orders = $this->data()->getOrderList();
            $start = ($request->getVar('start')) ? (int)$request->getVar('start') : 0;
            $records = PaginatedList::create($orders, $request);
            $records->setPageStart($start);
            $records->setPageLength($this->data()->PerPage);
        } else {
            $records = ArrayList::create();
        }

        $this->extend('updateOrderPaginatedList', $records);

        $this->order_paginated_list = $records;

        return $this;
    }

    /**
     * @return PaginatedList
     */
    public function OrderPaginatedList()
    {
        if (!$this->order_paginated_list) {
            $this->setOrderPaginatedList();
        }

        return $this->order_paginated_list;
    }
}
