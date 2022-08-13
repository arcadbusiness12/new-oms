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
class SettingModel extends AbstractDressFairOpenCartModel{
    protected $table = 'setting';
    protected $primaryKey = 'setting_id';

    const FIELD_STORE_ID = 'store_id';
    const FIELD_CODE = 'code';
    const FIELD_KEY = 'key';
    const FIELD_VALUE = 'value';
}