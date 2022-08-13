<?php

namespace App\Models\DressFairOpenCart\Customers;
use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/*
 * Opencart Customer Model to connect with customer
 */

/**
 * Description of CustomersModel
 *
 * @author kamran
 */

class CurrencyModel extends AbstractDressFairOpenCartModel
{
	protected $table = 'currency';
	protected $primaryKey = 'currency_id';

}
