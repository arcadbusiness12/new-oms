<?php

namespace App\Models\OpenCart\Customers;
use App\Models\OpenCart\AbstractOpenCartModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\Reseller\AccountModel;

/*
 * Opencart Customer Model to connect with customer
 */

/**
 * Description of CustomersModel
 *
 * @author kamran
 */

class CustomersModel extends AbstractOpenCartModel
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

	public function orders() {
		return $this->hasMany(OrdersModel::class, 'customer_id');
	}

	public function address() {
		return $this->hasMany(AddressModel::class, 'customer_id');
	}
	
}
