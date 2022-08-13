<?php
/*
 * Country Phonecode table model
 */
namespace App\Models\OpenCart\Customers;
use App\Models\OpenCart\AbstractOpenCartModel;
/**
 * Description of CountryPhoneCodeModel
 *
 * @author kamran
 */
class CountryPhoneCodeModel extends AbstractOpenCartModel{
    protected $table = 'country_phone_code';
    protected $primaryKey = 'id';

    const FIELD_ID = 'id';
    const FIELD_ISO = 'iso';
    const FIELD_NAME = 'name';
    const FIELD_NICENAME = 'nicename';
    const FIELD_ISO3 = 'iso3';
    const FIELD_NUMCODE = 'numcode';
    const FIELD_PHONECODE = 'phonecode';
}