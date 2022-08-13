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
class CountryModel extends AbstractDressFairOpenCartModel{
    protected $table = 'country';
    protected $primaryKey = 'country_id';

    const FIELD_COUNTRY_ID = 'country_id';
    const FIELD_NAME = 'name';
    const FIELD_ISO_CODE_2 = 'iso_code_2';
    const FIELD_ISO_CODE_3 = 'iso_code_3';
    const FIELD_ADDRESS_FORMAT = 'address_format';
    const FIELD_POSTCODE_REQUIRED = 'postcode_required';
    const FIELD_STATUS = 'status';
}