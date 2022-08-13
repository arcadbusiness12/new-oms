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

class CustomersModel extends AbstractDressFairOpenCartModel
{
	protected $table = 'customer';
	protected $primaryKey = 'customer_id';

	public static function getCustomerNames($name){
		$fullname = explode(" ", $name);
		$firstname = $fullname[0];
		unset($fullname[0]);
		$lastname = '';
		if(isset($fullname[1])){
			$lastname = implode(" ", $fullname);
		}

		return array('firstname' => $firstname, 'lastname' => $lastname);
	}
}
