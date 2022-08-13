<?php
/*
 * Exchange Order table model
 */
namespace App\Models\OpenCart\ExchangeOrders;
use App\Models\OpenCart\AbstractOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class AreaModel extends AbstractOpenCartModel{
	protected $table = 'area';
	protected $primaryKey = 'area_id';

	const FIELD_AREA_ID = 'area_id';
	const FIELD_ZONE_ID = 'zone_id';
	const FIELD_NAME = 'name';
	const FIELD_STATUS = 'status';
}