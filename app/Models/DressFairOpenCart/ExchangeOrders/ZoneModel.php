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
class ZoneModel extends AbstractDressFairOpenCartModel{
    protected $table = 'zone';
    protected $primaryKey = 'zone_id';

    const FIELD_ZONE_ID = 'zone_id';
    const FIELD_COUNTRY_ID = 'country_id';
    const FIELD_NAME = 'name';
    const FIELD_CODE = 'code';
    const FIELD_STATUS = 'status';
}