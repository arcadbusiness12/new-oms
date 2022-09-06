<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;

/**
 * Description of TfmExpress
 *
 * @author kamran
 */
class NexCourier implements ShippingProvidersInterface
{

	const DUMMY_AWB = 'TFM_';
	const API_CREATE_AWB = '/rest/addAwb';
	const API_EDIT_AWB = '/rest/editAwb';
	const API_AWB_NUMBER = 'orderID';
	const API_KEY = 'apiKey';

	protected $accountNumber;
	protected $apiUrl;
	protected $userName;

	public function __construct()
	{
		// $this->accountNumber = config('services.deliveryPanda')['accountNumber'];
		$this->apiUrl = config('services.nexCourier')['url'];
		// $this->userName = config('services.deliveryPanda')['userName'];
		// $this->password = config('services.deliveryPanda')['password'];
		$this->apiKey = config('services.nexCourier')['apiKey'];
		$this->httpClient = \App::make('httpClient'); // Http Client

		if ( null == $this->apiUrl)
		{
			throw new \Exception("ApiKey,Account Number and user Name for Tfm Express is required");
		}
	}

	public function forwardOrder($orders) {
		$returnResponse = [];
		// dd($orders);
		foreach ($orders as $key => $order) {
			if( trim($order->getPaymentMethod()) == 'cod' || trim($order->getPaymentMethod()) == 'cod_order_fee' || trim($order->getPaymentMethod()) == '' ){
				$payment_method = "COD";
				$NcndAmount = $order->getOrderTotalAmount(); 
			}else{
				$payment_method = "Prepaid";
				$NcndAmount = "0.00"; 
			}
      if($order->getStore()==2){
        $FromCompany   = "DressFair";
        $origin_address_line1 = "IndustrialArea 11";
        $origin_address_line2  = "Sharjah";
        $FromContactno = "971565651133";
        $origin_postal_code = "";
      }else{
        $FromCompany   = "BusinessArcade";
        $origin_address_line1 = "International City";
        $origin_address_line2  = "Dubai";
        $FromContactno = "971565634477";
        $origin_postal_code = "DXB";
      }
      $order_id = $order->getOrderID();
      if (strpos($order_id, '-2') !== false) {
        $type_of_order = "Reverse";
      }else{
        $type_of_order = "Forward";
      }
			try
			{
          $bookingData  = [
            'merchantCode' => 'deals',
            'processDefinitionCode' => 'order_booking',
            'processData' => [
              'order_number' => "$order_id",
              'type_of_order' => $type_of_order,
              'type_of_service' => 'Standard',
              'payment_mode' => $payment_method,
              'invoice_value' => $NcndAmount,
              'origin_name' => $FromCompany,
              'origin_address_line1' => $origin_address_line1,
              'origin_address_line2' => $origin_address_line2,
              'origin_contact_number' => $FromContactno,
              'origin_postal_code' => $origin_address_line2,
              'destination_name' => trim($order->getCustomerName()),
              'destination_address_line1' => trim($order->getCustomerArea()).", ".trim($order->getCustomerAddress()),
              'destination_address_line2' => '',
              'destination_landmark' => '',
              'destination_contact_number' => str_replace('+', '', $order->getCustomerMobileNumber()),
              'destination_postal_code' => trim($order->getCustomerCity()),
              'destination_city' => trim($order->getCustomerCity()),
              'destination_country' => 'United Arab Emirates',
              'number_of_shipments' => $order->getTotalItemsQuantity(),
              'additional_tracking_1' => '',
              'amount_to_be_collected' => $NcndAmount,
              'code_of_description' => $order->getGoodsDescription(),
              // 'customer_interation_link' => '',
              'package_type' => 'Dry',
              // 'item_reference_number_1' => '',
              // 'item_name_1' => '',
              // 'item_code_1' => '',
              // 'item_length_1' => '',
              // 'item_width_1' => '',
              // 'item_height_1' => '',
              'item_weight_1' => '',
              // 'item_value_1' => '',
              // 'item_special_instruction_1' => 'NO',
              // 'item_awb_number_2' => '',
            ]
          ]; 
					$bookingData = json_encode($bookingData);
							//$dest_url = $this->apiUrl."CustomerBooking";
							$dest_url = $this->apiUrl.$this->apiKey;
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => $dest_url,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 30,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "POST",
								CURLOPT_POSTFIELDS => $bookingData,
								CURLOPT_HTTPHEADER => array(
									"Content-Type: application/json"
								),
							));
							// curl_exec($curl);
							$response = json_decode(curl_exec($curl), true);
							// echo $this->apiKey;
							// echo "test<pre>"; print_r($response); die("testing...");
							$err = curl_error($curl);
							curl_close($curl);

							if ($err) {
								throw new \Exception($err);
							} else {
								$airway_billno = "";
								$pdf_link = "";
                $tracking_number_data = "";
                if( $response['successCount']  ){
                  $tracking_number_list =  $response['tracking_number_list'];
                  // echo "<pre>"; print_r($tracking_number_list);
                  if( is_array( $tracking_number_list ) ){
                    foreach( $tracking_number_list as $reference_number => $tracking_number_data ){
                      // echo $reference_number."<br>";
                      // echo "<pre>"; print_r($tracking_number_data);
                      $airway_billno = $tracking_number_data['reference_number'];
								      $pdf_link      = $tracking_number_data['pdf'];
                    }
                  }
                }
								$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $airway_billno,
								self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage',
								'pdf_print_link' => $pdf_link
							];
						}
					} catch (\Exception $ex) {
						continue;
					}
				}
				// echo $this->apiUrl;
				// dd($returnResponse);
				return $returnResponse;
	}
    public function cleanText($text = ''){
    	if($text){
    		$text = str_replace("'", "", $text);
    		$text = str_replace('"', '', $text);
    		$text = str_replace('"', '', $text);
    		$text = str_replace('<', '', $text);
    		$text = str_replace('>', '', $text);
    		$text = str_replace("\n", ' ', $text);
    		$text = str_replace("\r", ' ', $text);
    		$text = str_replace("\r\n", ' ', $text);
    		return $text;
    	}else{
    		return $text;
    	}
    }
    public function getAirwayBillUrl($awbNumber = null)
    {
			// return self::HASH_LINK;
			return route('deliverypanda_invoice', $awbNumber);
    }

    public function getOrderStatus()
    {

    }

    public function printAirwaybill()
    {

    }

    public static function getTrackingUrl($awbNumber = null)
    {
    	return self::HASH_LINK;
    }
  }