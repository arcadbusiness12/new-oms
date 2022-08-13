<?php
/*
 * Exchange Order table model
 */
namespace App\Models\DressFairOpenCart\ExchangeOrders;
use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class AreaModel extends AbstractDressFairOpenCartModel{
    protected $table = 'area';
    protected $primaryKey = 'area_id';

    const FIELD_AREA_ID = 'area_id';
    const FIELD_ZONE_ID = 'zone_id';
    const FIELD_NAME = 'name';
    const FIELD_STATUS = 'status';
}