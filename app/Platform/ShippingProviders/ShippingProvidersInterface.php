<?php

namespace App\Platform\ShippingProviders;

/**
 * Shipping Providers interface. Every shipping provider should implement this interface
 * Like GetGive / MaraXpress / Fetcher / Self
 * This gives uniform way to forward an order and generate airway bill
 * @author Kamran
 */
interface ShippingProvidersInterface
{

	const AIRWAYBILL_NUMBER = 'awbNumber';
	const MESSAGE_FROM_PROVIDER = 'msg';
	const HASH_LINK = '#';

	public function forwardOrder($orders);

	public function getOrderStatus();

	public function printAirwaybill();

	public function getAirwayBillUrl($awbNumber);

	public static function getTrackingUrl($awbNumber);
}
