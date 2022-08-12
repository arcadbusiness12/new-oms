<?php

namespace App\Models\Oms\PurchaseManagement;
interface OmsPurchaseShippedOrdersStatusInterface
{
	const PURCHASE_SHIPPED_STATUS_SHIPPED_FORWARDER = 1;
	const PURCHASE_SHIPPED_STATUS_SHIPPED = 2;
	const PURCHASE_SHIPPED_STATUS_DELIVERED = 3;
	const PURCHASE_SHIPPED_STATUS_CANCELLED = 5;
}