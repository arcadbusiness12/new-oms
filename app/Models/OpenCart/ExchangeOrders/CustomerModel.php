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
class CustomerModel extends AbstractOpenCartModel{
    protected $table = 'setting';
    protected $primaryKey = 'setting_id';

    const FIELD_STORE_ID = 'store_id';
    const FIELD_CODE = 'code';
    const FIELD_KEY = 'key';
    const FIELD_VALUE = 'value';
}