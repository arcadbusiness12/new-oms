<?php

namespace App\Models\Oms\PurchaseManagement;
interface OmsPurchaseOrdersStatusInterface
{
	const PURCHASE_STATUS_PENDING = 0;
	const PURCHASE_STATUS_AWAIT = 1;
	const PURCHASE_STATUS_AWAIT_APPROVAL = 2;
	const PURCHASE_STATUS_CONFIRM = 3;
	const PURCHASE_STATUS_TO_BE_SHIPPED = 4;
	const PURCHASE_STATUS_SHIPPED = 5;
	const PURCHASE_STATUS_DELIVERED = 6;
	const PURCHASE_STATUS_CANCELLED = 7;
}