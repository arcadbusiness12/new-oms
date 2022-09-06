<?php

namespace App\Platform\ShippingProviders;

use App\Platform\ShippingProviders\ShippingProvidersInterface;

/**
 * Description of Jeebly
 *
 * @author kamran
 */
class Jeebly implements ShippingProvidersInterface {

	const DUMMY_AWB = 'JEEBLY_';
	const API_CREATE_AWB = '/rest/addAwb';
	const API_EDIT_AWB = '/rest/editAwb';
	const API_AWB_NUMBER = 'orderID';
	const API_KEY = 'apiKey';

	protected $accountNumber;
	protected $apiUrl;
	protected $apiKey;

	public function __construct() {
		$this->apiUrl = config('services.jeebly')['url'];
		$this->apiKey = config('services.jeebly')['apiKey'];
		$this->accountNumber = config('services.jeebly')['clientCode'];
		$this->httpClient = \App::make('httpClient'); // Http Client

		if (null == $this->accountNumber || null == $this->apiUrl || $this->apiKey == null) {
			throw new \Exception("ApiKey, Client Code for Jeebly is required");
		}
	}

	public function forwardOrder($orders) {
		$returnResponse = [];
		foreach ($orders as $key => $order) {
			if( trim($order->getPaymentMethod()) == 'cod' || trim($order->getPaymentMethod()) == 'cod_order_fee' || trim($order->getPaymentMethod()) == '' ){
				$payment_method = "Cash";
				$amount = $order->getOrderTotalAmount(); 
			}else{
				$payment_method = "Prepaid";
				$amount = "0"; 
			}
      if($order->getStore()==2){
        $company_name = "DressFair";
        $company_phone = "971565651133";
        $company_alt_phone = "971565651133";
        $company_address = "Industrial Area 11";
        $company_state = "Sharjah";
        $company_pin = "12352";
      }else{
        $company_name = "BusinessArcade";
        $company_phone = "971565634477";
        $company_alt_phone = "971565634477";
        $company_address = "INTL City, Dubai";
        $company_state = "Dubai";
        $company_pin = "12352";
      }
      $order_id = $order->getOrderID();
      if (strpos($order_id, '-2') !== false) {
        //return airwaybill
        $amount = "0";
      }
			try
			{
				$bookingData = '{
				    "consignments":[
				        {
				            "customer_code":"' . $this->accountNumber . '",
				            "reference_number":"",
				            "service_type_id":"PREMIUM",
				            "load_type":"NON-DOCUMENT",
				            "commodity_id":"1",
				            "description":"' . $order->getGoodsDescription() . '",
				            "cod_favor_of":"",
				            "cod_collection_mode":"'.$payment_method.'",
				            "dimension_unit":"kg",
				            "length":"0",
				            "width":"0",
				            "height":"0",
				            "weight_unit":"kg",
				            "weight":"0",
				            "declared_value":"'.$amount. '",
				            "cod_amount":"'.$amount. '",
				            "num_pieces":"' . $order->getTotalItemsQuantity() . '",
				            "customer_reference_number":"' . $order_id . '",
				            "origin_details":{
				                "name":"'.$company_name.'",
				                "phone":"'.$company_phone.'",
				                "alternate_phone":"'.$company_alt_phone.'",
				                "address_line_1":"'.$company_address.'",
				                "address_line_2":"",
				                "pincode":"'.$company_pin.'",
				                "city":"",
				                "state":"'.$company_state.'"
				            },
				            "destination_details":{
				                "name":"' . trim($order->getCustomerName()) . '",
				                "phone":"' . str_replace('+', '', $order->getCustomerMobileNumber()) . '",
				                "alternate_phone":"' . str_replace('+', '', $order->getCustomerMobileNumber()) . '",
				                "address_line_1":"' . trim($order->getCustomerAddress()) . '",
				                "address_line_2":"",
				                "pincode":"' . $order->getCustomerPincode() . '",
				                "city":"' . trim($order->getCustomerCity()) . '",
				                "state":"' . trim($order->getCustomerArea()) . '"
				            },
				            "pieces_detail":[';
				$i = 1;
				foreach ($order->getOrderItems() as $key => $value) {
					$bookingData .= '{
				                    "description":"' . $value['description'] . '",
				                    "declared_value":"' . $value['order_value_per_unit'] * $value['quantity'] . '",
				                    "weight":"' . round($value['weight'], 2) . '",
				                    "height":"' . round($value['height'], 2) . '",
				                    "length":"' . round($value['length'], 2) . '",
				                    "width":"' . round($value['width'], 2) . '"';
					if ($i == count($order->getOrderItems())) {
						$bookingData .= '}';
					} else {
						$bookingData .= '},';
					}
					$i++;
				}
				$bookingData .= ']
				        }
				    ]
				}';

				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $this->apiUrl . "softdata",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $bookingData,
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"api-key: " . $this->apiKey,
					),
				));
				$response = json_decode(curl_exec($curl), true);
				$err = curl_error($curl);
				curl_close($curl);

				if ($err) {
					throw new \Exception($err);
				} else {
					$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $response['data'][0]['reference_number'],
					    self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage'
					];
				}
			} catch (\Exception $ex) {
				continue;
			}
		}
    // print_r($returnResponse); die;
		return $returnResponse;
	}

	public function invoice($reference_number = null){
		$curl = curl_init();
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
			echo $response;
		}
	}

	public function getAirwayBillUrl($awbNumber = null) {
		return route('jeebly_invoice', $awbNumber);
	}

	public function getOrderStatus() {

	}

	public function printAirwaybill() {

	}

	public static function getTrackingUrl($awbNumber = null) {
		/*$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->apiUrl . "track",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "reference_number=" . $awbNumber,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"api-key: " . $this->apiKey,
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			echo $response;
		}*/

		return self::HASH_LINK;
	}
}