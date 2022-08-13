<?php

namespace App\Models\OpenCart\Customers;
use App\Models\OpenCart\AbstractOpenCartModel;

/*
 * Opencart Customer Model to connect with customer
 */

/**
 * Description of CustomersModel
 *
 * @author kamran
 */

class CurrencyModel extends AbstractOpenCartModel
{
	protected $table = 'currency';
	protected $primaryKey = 'currency_id';

}
