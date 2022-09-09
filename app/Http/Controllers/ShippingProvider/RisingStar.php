<?php

namespace App\Http\Controllers\ShippingProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Session;
use Excel;

/**
* Description of PurchaseController
*
* @author kamran
*/
class RisingStar extends Controller
{
	protected $accountNumber;
	protected $apiUrl;
	protected $apiKey;

	public function __construct() {
		$this->apiUrl = config('services.risingStar')['url'];
		$this->apiKey = config('services.risingStar')['apiKey'];
		$this->accountNumber = config('services.risingStar')['clientCode'];

		if (null == $this->accountNumber || null == $this->apiUrl || $this->apiKey == null) {
			throw new \Exception("ApiKey, Client Code for Jeebly is required");
		}
	}
	public function index(){
		return redirect('/');
	}
	public function invoice($reference_number = null){
		$curl = curl_init();
		// die($this->apiUrl);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->apiUrl . "shippinglabel/stream?reference_number=" . $reference_number,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"api-key: " . $this->apiKey,
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			die($err);
		} else {
			header('Content-Type: application/pdf');
			// header('Content-Type: application/json');
			echo $response;
		}
	}
	// public function invoice($reference_number = null){
	// 	$bulk_respose = [];
	// 	for($z=0; $z<3; $z++){
	// 		$curl = curl_init();
	// 	// die($this->apiUrl);
	// 		curl_setopt_array($curl, array(
	// 			CURLOPT_URL => $this->apiUrl . "shippinglabel/stream?reference_number=" . $reference_number,
	// 			CURLOPT_RETURNTRANSFER => true,
	// 			CURLOPT_ENCODING => "",
	// 			CURLOPT_MAXREDIRS => 10,
	// 			CURLOPT_TIMEOUT => 30,
	// 			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 			CURLOPT_CUSTOMREQUEST => "GET",
	// 			CURLOPT_HTTPHEADER => array(
	// 				"Content-Type: application/json",
	// 				"api-key: " . $this->apiKey,
	// 			),
	// 		));
	// 		$response = curl_exec($curl);
	// 		$err = curl_error($curl);
	// 		curl_close($curl);
	// 		$bulk_respose[]=$response;
	// 	}

	// 	if ($err) {
	// 		die($err);
	// 	} else {
	// 		header('Content-Type: application/pdf');
	// 		header('Content-Type: application/json');
	// 		echo $bulk_respose;
	// 		foreach ($bulk_respose as $value) {
	// 		// echo "test";
	// 		}
	// 		// echo implode("", $bulk_respose);
	// 	}
	// }
}